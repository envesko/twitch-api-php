# Upgrading

## From nicklaw5/twitch-api-php

This package is a drop-in replacement. The namespace is unchanged, every class
and method keeps its name, and no signature has changed:

```bash
composer remove nicklaw5/twitch-api-php
composer require envesko/twitch-api-php
```

Composer will refuse to install both at once, which is deliberate. They would
otherwise collide on the `TwitchApi\` namespace and the winner would be
whichever autoloader happened to run last.

If you are coming from 7.2.0, read the next section as well.

## 7.2 to 7.3

There are no breaking changes. The public surface went from 217 methods to 310.
Nothing was removed and no signature changed, which is checked by comparing a
reflection dump of both releases.

### Three things that behave differently

#### Four EventSub subscriptions changed version

`subscribeToChannelUpdate()` and the three hype train helpers registered
version 1, which Twitch stopped accepting. Every call was failing with a 400,
so nothing that worked stops working. They now register version 2.

The event payload your webhook receives for these differs between v1 and v2.
If you wrote a handler against the v1 shape from the documentation, without
ever having received one, check it against the v2 shape.

#### Your own headers no longer displace the Client-ID

Passing a `headers` array to `HelixGuzzleClient` used to replace the whole
array, taking the `Client-ID` and `Content-Type` with it, so Twitch answered
every Helix call with `401 Client ID and OAuth token do not match`. Headers now
merge per header.

If you compensated by adding the `Client-ID` back yourself, you can stop.
Leaving it in is harmless: an explicit value still wins, matched
case-insensitively.

The same applies to a custom Guzzle handler, which used to discard the base URI
along with the headers.

#### Signature verification rejects malformed headers

`EventSubApi::verifySignature()` used to raise a warning when given a signature
header it could not parse. Under an error handler that promotes warnings to
exceptions, an unauthenticated request could take your webhook endpoint down.
It now returns `false`. Hash comparison is also constant time.

### What is new that you may want

- Full Helix coverage. If you hand-rolled a request for a missing endpoint,
  there is now a method for it.
- WebSocket and conduit EventSub transports, alongside webhook.
- `ConduitsApi`, `GuestStarApi`, `ExtensionsApi`, `SharedChatApi` and
  `ContentClassificationLabelsApi`.

### What is still broken in 7.3

Deliberately, because fixing them needs a major version:

- `WebhooksSubscriptionApi` is fatal when constructed without an explicit
  client, and wraps an endpoint Twitch retired in 2021. Use `EventSubApi`.
- Query parameter values are not URL encoded. A value containing `&`, `+` or
  `#` does not reach Twitch intact, and a caller-supplied value can append
  query parameters of its own to the request. This is fixed in 8.0 rather than
  here, because encoding would double-encode anyone who worked around it by
  pre-encoding their values, and that is a breaking change. If you have such a
  workaround, keep it for now; 8.0 tells you when to remove it.
- `OauthApi::getAuthUrl()` does not encode its parameters either, so a scope
  list or a redirect URI with a query string produces a malformed URL.
- Six methods call endpoints Twitch has withdrawn. They fail at the API, not in
  this library.

See the [changelog](CHANGELOG.md) for the full list.

## 7.x to 8.0

Not yet released. 8.0 raises the minimum PHP to 8.3, removes the withdrawn
endpoints and the dead classes above, replaces the internal parameter shape,
and introduces a typed exception hierarchy.

It also starts URL encoding query parameter values, which is the reason that
fix is not in 7.3. **If you pre-encode values before passing them in, as a
workaround for the current behaviour, you must remove that when you upgrade to
8.0 or your values will be encoded twice.** This section will be written in
full against the actual release.
