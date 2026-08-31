<?php

declare(strict_types=1);

namespace TwitchApi\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use TwitchApi\HelixGuzzleClient;
use TwitchApi\RequestGenerator;

/**
 * Coverage of the Helix surface, measured rather than asserted in a docblock.
 *
 * Every public method on every resource class is driven against a mock transport and the verb
 * and path it actually sends are recorded. A method whose endpoint string is wrong, or which
 * quietly sends the wrong verb, shows up here as a missing endpoint rather than passing on a
 * mocked expectation that agrees with the mistake.
 */
class EndpointCoverageTest extends TestCase
{
    /**
     * Endpoints the library calls that Twitch has since withdrawn. They are kept for the whole
     * of 7.x because removing them is a breaking change, and are deleted in 8.0.
     */
    private const WITHDRAWN = [];

    /**
     * Methods this harness cannot drive, because a plausible argument cannot be guessed from
     * the signature alone. Both are covered directly elsewhere.
     */
    private const NOT_DRIVABLE = [
        'EventSubApi::verifySignature',
    ];

    /**
     * @return array<string, list<string>> endpoint => methods that call it
     */
    private function drive(): array
    {
        $dir = __DIR__.'/../../src/Resources';
        $covered = [];

        foreach (scandir($dir) as $file) {
            if (substr($file, -4) !== '.php' || $file === 'AbstractResource.php') {
                continue;
            }

            $class = 'TwitchApi\\Resources\\'.substr($file, 0, -4);
            $ref = new \ReflectionClass($class);

            foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->isConstructor() || $method->getDeclaringClass()->getName() !== $class) {
                    continue;
                }

                $label = $ref->getShortName().'::'.$method->getName();
                if (in_array($label, self::NOT_DRIVABLE, true)) {
                    continue;
                }

                $history = [];
                $stack = HandlerStack::create(new MockHandler([new Response(200, [], '{}')]));
                $stack->push(Middleware::history($history));
                $api = new $class(
                    new HelixGuzzleClient('TEST_CLIENT_ID', ['handler' => $stack]),
                    new RequestGenerator()
                );

                $args = [];
                foreach ($method->getParameters() as $i => $parameter) {
                    $type = $parameter->getType();
                    // A few older signatures leave the parameter untyped, so the default value
                    // is the only clue to its shape.
                    if ($type === null && $parameter->isDefaultValueAvailable() && is_array($parameter->getDefaultValue())) {
                        $args[] = ['k' => 'v'];
                        continue;
                    }
                    $name = $type ? $type->getName() : 'string';
                    if ($name === 'int') {
                        $args[] = 1;
                    } elseif ($name === 'bool') {
                        $args[] = true;
                    } elseif ($name === 'array') {
                        $args[] = ['k' => 'v'];
                    } else {
                        $args[] = $i === 0 ? 'TEST_TOKEN' : 'x';
                    }
                }

                try {
                    $method->invokeArgs($api, $args);
                } catch (\Throwable $e) {
                    $this->fail(sprintf('%s could not be driven: %s', $label, $e->getMessage()));
                }

                $this->assertNotEmpty($history, $label.' sent no request');

                $request = $history[0]['request'];
                $path = preg_replace('#^helix/#', '', ltrim($request->getUri()->getPath(), '/'));
                $covered[$request->getMethod().' '.$path][] = $label;
            }
        }

        return $covered;
    }

    public function testEveryDocumentedEndpointIsCovered(): void
    {
        $documented = array_filter(array_map('trim', file(__DIR__.'/endpoints.txt')));
        $covered = array_keys($this->drive());

        $missing = array_values(array_diff($documented, $covered));

        $this->assertSame(
            [],
            $missing,
            sprintf('%d documented Helix endpoints have no method', count($missing))
        );
        $this->assertCount(149, $documented, 'The documented endpoint list changed size');
    }

    public function testNoEndpointIsCalledThatTwitchNoLongerDocuments(): void
    {
        $documented = array_filter(array_map('trim', file(__DIR__.'/endpoints.txt')));
        $covered = array_keys($this->drive());

        $undocumented = array_values(array_diff($covered, $documented));
        sort($undocumented);

        $expected = self::WITHDRAWN;
        sort($expected);

        // Anything new here is either a typo in an endpoint string or an endpoint Twitch has
        // dropped since this list was written.
        $this->assertSame($expected, $undocumented);
    }

    public function testEveryResourceClassIsReachableFromTheFacade(): void
    {
        $dir = __DIR__.'/../../src/Resources';
        $api = new \TwitchApi\TwitchApi(
            new HelixGuzzleClient('TEST_CLIENT_ID'),
            'TEST_CLIENT_ID',
            'TEST_CLIENT_SECRET'
        );

        $reachable = [];
        foreach (get_class_methods($api) as $method) {
            if (strpos($method, 'get') === 0) {
                $reachable[] = get_class($api->$method());
            }
        }

        foreach (scandir($dir) as $file) {
            if (substr($file, -4) !== '.php' || $file === 'AbstractResource.php') {
                continue;
            }
            $class = 'TwitchApi\\Resources\\'.substr($file, 0, -4);
            $this->assertContains($class, $reachable, $class.' has no getter on TwitchApi');
        }
    }
}
