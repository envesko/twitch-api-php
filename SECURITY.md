# Security policy

## Supported versions

Security fixes are applied to the latest minor release. Older minors are not
patched.

## Reporting a vulnerability

Do not open a public issue.

Report it privately through GitHub:
[open a security advisory](https://github.com/envesko/twitch-api-php/security/advisories/new).
Private reporting is enabled on this repository, so the report stays between
you and the maintainers until a fix is released.

Please include the library version, the PHP version, and enough detail to
reproduce.

## Scope

This library holds OAuth client credentials, builds authenticated requests, and
verifies inbound EventSub webhook signatures. Anything affecting those is in
scope, including:

- a way to make `verifySignature()` accept a signature it should reject
- credentials or tokens appearing in an exception message, log line or URL
- a request built such that caller-supplied input can alter its target or its
  other parameters

Out of scope: vulnerabilities in Twitch's API itself, and anything requiring a
consumer to pass values they control to a parameter documented as trusted.

## Known issues

Some defects are documented rather than fixed, because fixing them requires a
breaking change. They are listed under "Known broken" in the
[changelog](CHANGELOG.md) and are addressed in 8.0. Reports about those are
welcome but are not treated as new vulnerabilities.
