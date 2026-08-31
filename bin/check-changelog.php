<?php

/*
 * Verifies that CHANGELOG.md has a real, dated entry for a version before it is
 * released.
 *
 * 7.3.0 shipped with its entry still sitting under "## [Unreleased]", because
 * nothing checked. Run this from the release workflow on every tag, and locally
 * with `make release-check VERSION=8.0.0` before tagging.
 *
 * Usage: php bin/check-changelog.php 8.0.0
 */

declare(strict_types=1);

$version = $argv[1] ?? '';

if ($version === '') {
    fwrite(STDERR, "usage: php bin/check-changelog.php <version>\n");
    exit(2);
}

$version = ltrim($version, 'vV');

if (!preg_match('/^\d+\.\d+\.\d+(?:-[0-9A-Za-z.-]+)?$/', $version)) {
    fwrite(STDERR, sprintf("not a version: %s\n", $version));
    exit(2);
}

$path = dirname(__DIR__).'/CHANGELOG.md';
$changelog = file_get_contents($path);

if ($changelog === false) {
    fwrite(STDERR, sprintf("cannot read %s\n", $path));
    exit(2);
}

$lines = preg_split('/\R/', $changelog) ?: [];
$quoted = preg_quote($version, '/');

$headingIndex = null;
$problems = [];

foreach ($lines as $i => $line) {
    if (!preg_match('/^## \['.$quoted.'\](.*)$/', $line, $match)) {
        continue;
    }

    if ($headingIndex !== null) {
        $problems[] = sprintf('There is more than one "## [%s]" heading.', $version);
        break;
    }

    $headingIndex = $i;
    $rest = trim($match[1]);

    if (!preg_match('/^- (\d{4})-(\d{2})-(\d{2})$/', $rest, $date)) {
        $problems[] = sprintf(
            'The "## [%s]" heading reads "%s". It needs a release date: "## [%s] - %s".',
            $version,
            trim($line),
            $version,
            date('Y-m-d')
        );

        continue;
    }

    if (!checkdate((int) $date[2], (int) $date[3], (int) $date[1])) {
        $problems[] = sprintf('"%s" is not a real date.', $rest);
    }
}

if ($headingIndex === null) {
    $problems[] = sprintf(
        'CHANGELOG.md has no "## [%s]" heading. Rename "## [Unreleased]" to '
        .'"## [%s] - %s" and leave a fresh, empty "## [Unreleased]" above it.',
        $version,
        $version,
        date('Y-m-d')
    );
}

if ($headingIndex !== null && $problems === []) {
    $body = '';

    for ($i = $headingIndex + 1, $count = count($lines); $i < $count; ++$i) {
        if (str_starts_with($lines[$i], '## ')) {
            break;
        }

        $body .= $lines[$i];
    }

    if (trim($body) === '') {
        $problems[] = sprintf('The "## [%s]" section is empty.', $version);
    }
}

if ($problems !== []) {
    fwrite(STDERR, sprintf("CHANGELOG.md is not ready to release %s.\n\n", $version));

    foreach ($problems as $problem) {
        fwrite(STDERR, sprintf("  - %s\n", $problem));
    }

    fwrite(STDERR, "\n");

    exit(1);
}

printf("CHANGELOG.md has a dated entry for %s.\n", $version);
