#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Fails the build if loading or exercising the library emits a diagnostic of its own.
 *
 * Most of these notices are raised when a class is compiled rather than when a method is
 * called, so a normal test run can pass while every consumer on a newer PHP fills its log.
 * This walks every class in src/ so the compile-time ones are seen, then instantiates the
 * facade and touches every getter so the lazily built resources are compiled too.
 *
 * Usage: php bin/check-deprecations.php
 * Exits 0 when clean, 1 when the library emitted anything.
 */

require __DIR__ . '/../vendor/autoload.php';

$srcDir = realpath(__DIR__ . '/../src');

// Legacy namespace shims from the v4 rename. src/NewTwitchApi.php declares a namespace that
// does not match its PSR-4 path and extends a class that does not exist, so autoloading it is
// fatal rather than merely noisy. Both are scheduled for removal in v8.
$skip = ['NewTwitchApi'];

$diagnostics = [];

set_error_handler(static function (int $severity, string $message, string $file = '', int $line = 0) use ($srcDir, &$diagnostics): bool {
    // Only the library's own output is a build failure. A vendor package emitting its own
    // deprecations is that package's problem and must not mask or fail our result.
    if ($file === '' || strpos(realpath($file) ?: $file, $srcDir) !== 0) {
        return true;
    }

    $labels = [
        E_DEPRECATED => 'Deprecated',
        E_USER_DEPRECATED => 'Deprecated',
        E_WARNING => 'Warning',
        E_USER_WARNING => 'Warning',
        E_NOTICE => 'Notice',
        E_USER_NOTICE => 'Notice',
    ];

    if (!isset($labels[$severity])) {
        return true;
    }

    // Normalise separators so a baseline written on Windows still matches on CI.
    $relative = str_replace('\\', '/', substr(realpath($file) ?: $file, strlen($srcDir) + 1));
    $diagnostics[] = sprintf('%s: %s (%s:%d)', $labels[$severity], $message, $relative, $line);

    return true;
});

error_reporting(E_ALL);

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($srcDir));
$classes = [];
foreach ($files as $file) {
    if ($file->getExtension() !== 'php') {
        continue;
    }

    $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($srcDir) + 1));

    foreach ($skip as $skipped) {
        if (strpos($relative, $skipped) !== false) {
            continue 2;
        }
    }

    $classes[] = 'TwitchApi\\' . str_replace('/', '\\', substr($relative, 0, -4));
}

sort($classes);

// Compiling each class is what surfaces signature-level deprecations.
$loaded = 0;
foreach ($classes as $class) {
    if (class_exists($class) || interface_exists($class) || trait_exists($class)) {
        $loaded++;
    }
}

// Resources are built lazily, so the facade has to be walked to compile them all.
$client = new TwitchApi\HelixGuzzleClient('deprecation-check');
$api = new TwitchApi\TwitchApi($client, 'deprecation-check', 'deprecation-check');
foreach (get_class_methods($api) as $method) {
    if (strpos($method, 'get') === 0) {
        $api->$method();
    }
}

restore_error_handler();

printf("Checked %d classes on PHP %s.\n", $loaded, PHP_VERSION);

$diagnostics = array_values(array_unique($diagnostics));
sort($diagnostics);

// Some diagnostics cannot be fixed without a breaking change and are held for the next major.
// They live in a baseline so the check still fails on anything new. Run with --update-baseline
// to rewrite it after a deliberate fix or an accepted addition.
$baselineFile = __DIR__ . '/../.deprecation-baseline';
$baseline = [];
if (file_exists($baselineFile)) {
    foreach (file($baselineFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if ($line !== '' && $line[0] !== '#') {
            $baseline[] = $line;
        }
    }
}

if (in_array('--update-baseline', $argv ?? [], true)) {
    $header = "# Diagnostics emitted by src/ that are accepted for now.\n"
        . "# Each needs a breaking change to fix, so they are held for the next major version.\n"
        . "# Regenerate with: php bin/check-deprecations.php --update-baseline\n";
    file_put_contents($baselineFile, $header . implode("\n", $diagnostics) . "\n");
    printf("Baseline rewritten with %d entry(ies).\n", count($diagnostics));
    exit(0);
}

$new = array_values(array_diff($diagnostics, $baseline));
$fixed = array_values(array_diff($baseline, $diagnostics));

if ($baseline !== []) {
    printf("%d baselined diagnostic(s) ignored.\n", count($baseline) - count($fixed));
}

if ($fixed !== []) {
    // Not a failure. The set of diagnostics a given PHP emits varies by version, so an entry
    // that is absent here may simply belong to a version this run is not on. "Required
    // parameter follows optional", for instance, does not exist before 8.0, so every entry
    // would look resolved on the 7.4 job. Failing on that would make the build red on the
    // oldest supported version for no reason.
    printf("\n%d baselined diagnostic(s) did not occur on this PHP version:\n\n", count($fixed));
    foreach ($fixed as $diagnostic) {
        echo '  ' . $diagnostic . "\n";
    }
    echo "\nIf they are genuinely fixed on every supported version, refresh the baseline with:\n";
    echo "  php bin/check-deprecations.php --update-baseline\n";
}

if ($new === []) {
    echo "No new diagnostics emitted by src/.\n";
    exit(0);
}

printf("\n%d new diagnostic(s) emitted by src/:\n\n", count($new));
foreach ($new as $diagnostic) {
    echo '  ' . $diagnostic . "\n";
}
echo "\nThese reach every consumer on this PHP version. Fix them or the build stays red.\n";
exit(1);
