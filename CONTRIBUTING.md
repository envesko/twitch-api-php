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
4. Add a test in `test/TwitchApi/Resources/`. One per method, asserting the
   verb, endpoint and parameter map.
5. Add a line to the `## [Unreleased]` section of
   [CHANGELOG.md](CHANGELOG.md).
6. Run `make test`.

The coverage test reads `test/TwitchApi/endpoints.txt`. If Twitch has published
a new endpoint, add its line there too, or the test will tell you the list is
out of date.

## The test harness

PHPUnit, for everything. phpspec was removed in 8.0.0 because its newest release
cannot install above PHP 8.3.

Resource tests assert that a method delegates with the right verb, endpoint and
parameters, which is what a thin API wrapper needs. Everything with real
behaviour, the request layer and signature verification most of all, gets a
test that asserts what actually goes on the wire.

## What CI checks

`make test` runs, in order: coding standards, static analysis, a check that the
library emits no deprecations of its own, then PHPUnit. The first three fail
more often than the tests do.

- Coding standards are php-cs-fixer with PSR-2 plus Symfony. Run
  `vendor/bin/php-cs-fixer fix` before pushing.
- Static analysis is PHPStan at level 8. Do not add to the baseline to make an
  error go away.
- The deprecation check compiles every class and fails on any warning or
  deprecation raised from `src/`. These fire at compile time, so a passing test
  suite is not evidence that they are absent.

## Pull requests

One change per pull request. Describe what Twitch endpoint or behaviour it
relates to and link the reference page. If you are fixing a bug, a failing test
in the same pull request is the clearest way to show it.

## Releasing

Work lands under `## [Unreleased]` in [CHANGELOG.md](CHANGELOG.md) as it
happens, so the entry is written by whoever made the change rather than
reconstructed from commits months later.

Releasing means renaming that heading:

1. `## [Unreleased]` becomes `## [8.0.0] - 2026-08-30`, and a fresh, empty
   `## [Unreleased]` goes back above it.
2. `make release-check VERSION=8.0.0`.
3. Tag `v8.0.0` and push it.

Step 2 is the same check CI runs on the tag, so a tag pushed without step 1
fails. 7.3.0 was released with its entry still sitting under `## [Unreleased]`,
which is what that check exists to prevent.

## Relationship to the original project

This is a maintained continuation of
[nicklaw5/twitch-api-php](https://github.com/nicklaw5/twitch-api-php). We keep
the public API compatible with it on purpose, so anything that changes a method
signature or removes one needs a major version and a note in
[UPGRADING.md](UPGRADING.md).
