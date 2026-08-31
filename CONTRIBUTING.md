# Contributing

Thanks for helping. Most contributions here are one of two things: an endpoint
Twitch added, or a bug in how a request is built.

## Adding an endpoint

1. Find it in the [Helix reference](https://dev.twitch.tv/docs/api/reference/).
2. Add a method to the resource class that owns that area, following the shape
   of the methods already there. Required parameters are positional; optional
   ones are nullable with a `null` default and only added to the parameter map
   when set.
3. Link the reference page in the docblock with `@link`. Every method has one.
4. Add a phpspec example in `spec/TwitchApi/Resources/`. One per method,
   asserting the verb, endpoint and parameter map.
5. Run `make test`.

The coverage test reads `test/TwitchApi/endpoints.txt`. If Twitch has published
a new endpoint, add its line there too, or the test will tell you the list is
out of date.

## Which test harness

Both are in use and they are not redundant:

- **phpspec** for resource methods. It asserts that a method delegates with the
  right arguments, which is what a thin API wrapper needs.
- **PHPUnit** for anything with real behaviour: the request layer, signature
  verification, and the tests that assert what actually goes on the wire.

phpspec cannot install on PHP 8.4 or above, so it will be replaced in the next
major version. Until then, keep writing resource specs in phpspec.

## What CI checks

`make test` runs, in order: coding standards, static analysis, a check that the
library emits no deprecations of its own, PHPUnit, then phpspec. The first
three fail more often than the tests do.

- Coding standards are php-cs-fixer with PSR-2 plus Symfony. Run
  `vendor/bin/php-cs-fixer fix` before pushing.
- Static analysis is PHPStan at level 5, with a baseline for known-broken code.
  Do not add to the baseline to make an error go away.
- The deprecation check compiles every class and fails on any warning or
  deprecation raised from `src/`. These fire at compile time, so a passing test
  suite is not evidence that they are absent.

## Pull requests

One change per pull request. Describe what Twitch endpoint or behaviour it
relates to and link the reference page. If you are fixing a bug, a failing test
in the same pull request is the clearest way to show it.

## Relationship to the original project

This is a maintained continuation of
[nicklaw5/twitch-api-php](https://github.com/nicklaw5/twitch-api-php). We keep
the public API compatible with it on purpose, so anything that changes a method
signature or removes one needs a major version and a note in
[UPGRADING.md](UPGRADING.md).
