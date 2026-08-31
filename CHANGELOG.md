# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Entries from 7.3.0 onward are written as the work happens. Earlier entries are
summarised from release tags and commit history after the fact, so they record
what a release contained rather than every change in it.

## [Unreleased]

### Added

- 52 endpoints, bringing documented Helix coverage to 149 of 149.
- `ConduitsApi`, `GuestStarApi`, `ExtensionsApi`, `SharedChatApi` and
  `ContentClassificationLabelsApi`, with getters on `TwitchApi`.
- 34 EventSub subscription types, taking the helpers from 45 to 79. Covers
  AutoMod, the chat family, moderation and warnings, unban requests,
  suspicious users, shared chat, Guest Star, VIPs, ad breaks, bits, automatic
  and custom power-up redemptions, whispers, and conduit shards.
- WebSocket and conduit transports for EventSub, as
  `createEventSubSubscriptionViaWebSocket()` and
  `createEventSubSubscriptionViaConduit()`. The webhook path is unchanged.
- Endpoints added to existing classes: Get Ad Schedule, Snooze Next Ad, Get
  Custom Power-up, Get and Update Extension Bits Products, Send Chat Message,
  Get User Emotes, the four chat pin operations, Get Clips Download, Create
  Clip From VOD, Get Hype Train Status, Get Moderated Channels, the unban
  request pair, Warn Chat User, the suspicious user pair, Update User
  Extensions and Get Authorization By User.

### Fixed

- Query parameter values were interpolated into the URL unencoded. A value
  containing `&` split into extra parameters, a base64 pagination cursor was
  corrupted because its `+` decoded as a space, a `#` truncated the value into
  a URI fragment, and a user-supplied search term could append query
  parameters of its own to the outgoing request.
- The `Client-ID` header went missing whenever the caller passed a `headers`
  array of their own, because caller configuration replaced the whole array
  rather than merging into it. Twitch answered those calls with
  `401 Client ID and OAuth token do not match`.
  ([upstream #155](https://github.com/nicklaw5/twitch-api-php/issues/155))
- `HelixGuzzleClient` discarded its own base URI and `Client-ID` header
  whenever a handler was supplied, so any consumer adding retry or logging
  middleware got a client that could not reach Twitch.
- `EventSubApi::verifySignature()` raised a warning on a malformed signature
  header, which becomes an exception under an error handler that promotes
  warnings. It now rejects the request instead, and compares hashes in
  constant time.
- `channel.update` and the three hype train subscriptions registered version
  1, which Twitch no longer accepts, so every one of those calls failed with a
  400. They now register version 2.
  ([upstream #147](https://github.com/nicklaw5/twitch-api-php/issues/147))
- 163 parameters declared a type with a `null` default but no `?`, which PHP
  8.4 reports as deprecated once per parameter at class-compile time.
  ([upstream #153](https://github.com/nicklaw5/twitch-api-php/issues/153))

### Changed

- Resource classes are constructed on first use rather than in the `TwitchApi`
  constructor. A caller that needs one resource now compiles a handful of
  classes per request instead of all of them. No API change.
- The package is now `envesko/twitch-api-php`. The namespace is unchanged, and
  a `replace` entry means Composer will not install it alongside the original.

### Development

- CI covers PHP 7.4 through 8.3 with the full suite, and 8.4 and 8.5 with
  PHPUnit plus a check that the library emits no diagnostics of its own.
  phpspec cannot install above 8.3.
- PHPStan at level 5 with a baseline.
- Helix coverage is asserted by a test that drives every public method and
  compares the requests it sends against the documented endpoint list.

### Known broken

Real, and deliberately not fixed here because fixing them requires a breaking
change. All are removed or repaired in 8.0.

- `WebhooksSubscriptionApi` calls a static method that does not exist and is
  fatal when constructed without an explicit client. Twitch retired the
  endpoint it wraps in 2021; use `EventSubApi`.
- `src/NewTwitchApi.php` declares a namespace that does not match its path and
  is fatal to autoload.
- `ScheduleApi::getChanneliCalendar()` and two private EventSub helpers declare
  an optional parameter before a required one, which PHP has reported as
  deprecated since 8.0. Fixing it reorders parameters.
- `OauthApi::getAuthUrl()` does not encode its query parameters.
- Six methods call endpoints Twitch has withdrawn.

## [7.2.0] - 2023-06-13

Shoutouts, creator goals, Get Followed Channels and Get Channel Followers, an
entitlements filter, and version promotions for the `channel.follow`, shield
mode and charity campaign subscriptions.

## [7.1.0] - 2022-12-09

Charity campaign endpoints and subscriptions, chat and moderation additions,
`drop.entitlement.grant`, and optional body parameters on Update Drops
Entitlements.

## [7.0.0] - 2022-08-29

Raids and Whispers APIs. PHP 8.1 added to the test matrix.

## [6.1.0] - 2022-02-27

Update Drops Entitlements, and pagination support on Get EventSub
Subscriptions.

## [6.0.0] - 2021-07-30

The `NewTwitchApi` namespace became `TwitchApi`, with the old name kept as a
shim. Kraken v5 support dropped, along with Create and Delete User Follow.

## [5.0.0] - 2021-05-28

Polls, Predictions, stream markers and stream tags.

## [4.2.0] - 2021-03-23

Specs for the Users, Channels and Videos APIs.

## [4.1.0] - 2021-03-16

Teams endpoints with `getTeamByName` and `getTeamById`, and Check User
Subscription.

## [4.0.0] - 2021-02-25

Reworked body parameter handling across the resource classes.

## [3.3.0] - 2020-12-11

Delete User Follow, and body parameter validation in `AbstractResource`.

## [3.2.0] - 2020-12-07

Spec coverage and build fixes.

## [3.1.0] - 2020-09-20

Channel Points custom rewards and redemptions.

## Earlier

See the [release tags](https://github.com/envesko/twitch-api-php/tags) for
3.0.5 and earlier, back to the first release in February 2017.
