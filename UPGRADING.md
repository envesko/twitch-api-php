# Upgrading

8.0.0 is the current release and the one to be on. It fixes the things 7.x could
not fix without breaking, the most important being that query parameter values
now reach Twitch intact. Read the section below before taking it: seven things
change behaviour and three of them can fail silently.

> [!NOTE]
> **If you cannot take 8.0.0 yet, drop to 7.3.0:**
>
> ```bash
> composer require envesko/twitch-api-php:^7.3.0
> ```
>
> That is the 7.3.0 release of this library, not PHP 7.3. It runs on PHP 7.4
> upward, has the same 149 Helix endpoints and 79 EventSub subscription types,
> and is maintained. None of the 8.0.0 breaking changes are in it. Take it if
> you are below PHP 8.3, or if you want the coverage now and the behaviour
> changes on your own schedule.

Coming from `nicklaw5/twitch-api-php` rather than moving between versions of
this one? Read [From nicklaw5/twitch-api-php](#from-nicklaw5twitch-api-php)
first. It is a drop-in replacement and takes two commands.

## 7.x to 8.0.0

Minimum PHP is 8.3. Seven things change behaviour. Most fail loudly and you will
see them immediately. Three carry a case that does not raise anything at all,
and will not show up until a request reaches Twitch carrying the wrong data.
Those three are marked.

| What | How to tell | What to do |
| --- | --- | --- |
| **PHP 8.3 required** <br> loud | Composer refuses to install. | Upgrade PHP first, or stay on the library's 7.3.0 release, which is maintained and runs on PHP 7.4 upward. |
| **Pre-encoded query values are now double-encoded** <br> quiet | You call `rawurlencode()` or `urlencode()` on a value before passing it to a library method. Searches return nothing, or cursors stop working. | Remove your own encoding and pass the raw value. The library encodes now. |
| **Removed methods** <br> loud | Fatal error, undefined method. All of them called endpoints Twitch withdrew, so they were failing at the API already. | See the replacement table below. |
| **`isValidAccessToken()` returns false instead of throwing** <br> quiet | You wrapped it in try/catch and treated reaching the next line as "valid", without checking the return value. | Check the return value. If you already did, nothing changes: the false takes the branch your catch used to. |
| **Untyped parameters are now typed** <br> loud, except one | `TypeError` naming the parameter. | Pass the declared type. These were being silently coerced before. The exception is `searchChannels()`, below. |
| **`getAuthUrl()` output is now encoded** <br> loud, if you assert on it | A test comparing the authorize URL string fails. | Update the expected string. The old output was malformed whenever a scope list or a redirect with a query was involved. |
| **`refreshToken()` parameter renamed** <br> loud | Only if you call it with named arguments: `refreshToken(refeshToken: $t)`. | Spell it `refreshToken:`. |

### Removed, and what replaces it

| Gone | Use instead |
| --- | --- |
| `UsersApi::getUsersFollows()` | `ChannelsApi::getChannelFollowers()` or `ChannelsApi::getFollowedChannels()` |
| `HypeTrainApi::getHypeTrainEvents()` | `HypeTrainApi::getHypeTrainStatus()` |
| `TagsApi::replaceStreamTags()` | `ChannelsApi::modifyChannelInfo()`, `tags` field |
| `WebhooksApi`, `WebhooksSubscriptionApi`, `TwitchApi::getWebhooksApi()`, `TwitchApi::getWebhooksSubscriptionApi()` | `EventSubApi` |
| `EntitlementsApi::getCodeStatus()`, `::redeemCode()`, `::createEntitlementGrantsUploadURL()` | Nothing. Twitch removed the feature. |
| `src/NewTwitchApi.php`, the unloadable one | Nothing to do. It could never be autoloaded. |

### The legacy NewTwitchApi namespace still works

`NewTwitchApi\NewTwitchApi` and `NewTwitchApi\HelixGuzzleClient` have aliased
their current equivalents since the rename in 6.0.0, and continue to in 8.0.0.
They are two six-line classes, so there is no reason to make anyone rewrite
imports for them in this release.

They remain deprecated, though, and 8.0.0 is the last release guaranteed to
carry them. Moving your imports to `TwitchApi\` is a find and replace;
nothing else changes.

### If you pre-encode query values, stop

Through 7.x the library did not encode query parameters, so a value containing
`&`, `+` or `#` did not reach Twitch intact. If you worked around that by
encoding values yourself, you are now encoding them twice and the request will
carry the wrong value.

This one does not raise an error. Searches quietly return nothing, and
pagination cursors quietly stop advancing.

```php
// 7.x workaround. Remove it.
$api->getSearchApi()->searchCategories($token, rawurlencode($term));

// 8.0.0. Pass the raw value.
$api->getSearchApi()->searchCategories($token, $term);
```

To find them:

```bash
grep -rn "urlencode" --include="*.php" . | grep -i twitch
```

You are only affected if the encoded value is passed *into* a library method.
Encoding values for your own URLs elsewhere is unrelated and should stay.

### searchChannels, if you passed a string for $liveOnly

The third argument was untyped and is now `?bool`. Every other newly typed
parameter raises a `TypeError` when given the wrong thing. This one does not,
because a non-empty string coerces to `true`:

```php
// 7.x sent live_only=false. 8.0.0 sends live_only=1, the opposite.
$api->getSearchApi()->searchChannels($token, $term, 'false');

// Pass a real boolean and both releases agree.
$api->getSearchApi()->searchChannels($token, $term, false);
```

Only a string matters here. `false`, `null` and omitting the argument all behave
as they did. If your call site passes a literal `true` or `false`, or a variable
that holds one, there is nothing to do.

### What looks like it breaks but does not

The typed exceptions extend the Guzzle classes they replace, so an existing
`catch (GuzzleException $e)` still matches, and so does a PSR-18
`ClientExceptionInterface` catch. There are tests asserting exactly that. Adopt
`RateLimitException` and the rest when you want to, not because you have to.

### Two prompts for your agent

Both produce the same report. The first stops there. The second offers to fix
what it found, one change at a time, and asks before each. Use the first if you
want to know where you stand before deciding anything.

<details open>
<summary>Prompt: check my project against the 8.0.0 breaking changes</summary>

```
Check whether upgrading the `envesko/twitch-api-php` Composer package from 7.x
to 8.0.0 will break this project.

Report only. Do not change any files.

Work through these seven and answer each with YES (affected), NO (not affected)
or UNSURE, with the file and line for every YES:

1. PHP version. Does composer.json allow PHP below 8.3, or is the deployment
   target below 8.3? 8.0.0 requires 8.3 or later.

2. Pre-encoded query values. SILENT FAILURE. Search for `urlencode` and
   `rawurlencode`. Flag only where the encoded value is then passed INTO a
   method on this library. Encoding for your own URLs elsewhere is unrelated.

3. isValidAccessToken. SILENT FAILURE. Find every call. Flag it if the
   surrounding code treats reaching the next line as "the token is valid"
   without checking the returned boolean, or relies on catching an exception to
   mean "invalid". In 8.0.0 it returns false instead of throwing.

4. Removed methods. Search for `getUsersFollows`, `getHypeTrainEvents`,
   `replaceStreamTags`, `getCodeStatus`, `redeemCode`,
   `createEntitlementGrantsUploadURL`, `getWebhooksApi`,
   `getWebhooksSubscriptionApi`, `WebhooksApi` and `WebhooksSubscriptionApi`.
   All are gone in 8.0.0. The `NewTwitchApi\` namespace still works, so do not
   flag it.

5. Untyped parameters now typed. Find calls to `getUserAccessToken`,
   `createCustomReward`, `updateCustomReward`, `modifyChannelInfo`,
   `createPoll`, `updateChannelStreamSchedule`, `searchChannels`, and
   `HelixGuzzleClient::getConfig` or `::send`. Flag any passing a type other
   than the one 8.0.0 declares. These used to be coerced silently.
   SILENT FAILURE in one case: `searchChannels` takes `?bool $liveOnly` as its
   third argument. A string there, `'false'` most of all, coerces to true and
   inverts the filter with no error. Flag any non-boolean third argument.

6. getAuthUrl output. Does any test or code compare the authorize URL as a
   string? Its output is url encoded in 8.0.0.

7. refreshToken named argument. Is it ever called as
   `refreshToken(refeshToken: ...)`? The misspelling is corrected in 8.0.0.

8. Legacy namespace. ADVISORY, NOT BREAKING. Search for imports of
   `NewTwitchApi\`. These still work in 8.0.0 and nothing will break, but they
   are deprecated. Report them separately from the seven above, under a heading
   of "deprecated, not breaking", with the count of files affected.

Finish with:
- a one-line verdict: SAFE TO UPGRADE, or NOT SAFE, with the count of YES items
- the YES items in the order you would fix them, riskiest first
- anything you marked UNSURE and what you would need to resolve it
```

</details>

<details open>
<summary>Prompt: check, then patch my project for 8.0.0, one change at a time</summary>

```
Upgrade this project to `envesko/twitch-api-php` 8.0.0.

Phase one: report. Change nothing yet.

Produce exactly the report described below, then STOP and show it to me before
touching a single file. Do not begin phase two until I reply.

Work through these seven and answer each with YES, NO or UNSURE, with the file
and line for every YES:

1. PHP version. composer.json or the deployment target below 8.3.
2. Pre-encoded query values. SILENT FAILURE. `urlencode` or `rawurlencode` on a
   value passed INTO a library method.
3. isValidAccessToken. SILENT FAILURE. Code that treats no exception as
   "valid", or catches to mean "invalid", rather than reading the boolean.
4. Removed methods. `getUsersFollows`, `getHypeTrainEvents`,
   `replaceStreamTags`, `getCodeStatus`, `redeemCode`,
   `createEntitlementGrantsUploadURL`, `getWebhooksApi`,
   `getWebhooksSubscriptionApi`, `WebhooksApi`, `WebhooksSubscriptionApi`. The
   `NewTwitchApi\` namespace still works, so do not flag it.
5. Untyped parameters now typed. `getUserAccessToken`, `createCustomReward`,
   `updateCustomReward`, `modifyChannelInfo`, `createPoll`,
   `updateChannelStreamSchedule`, `searchChannels`,
   `HelixGuzzleClient::getConfig` and `::send`. SILENT FAILURE for
   `searchChannels`: a string third argument coerces to true and inverts the
   live filter with no error.
6. getAuthUrl output compared as a string anywhere.
7. refreshToken called with the misspelled named argument.
8. Legacy namespace. ADVISORY, NOT BREAKING. Imports of `NewTwitchApi\`.
   Deprecated but still working. List separately.

End the report with a verdict and the YES items ordered riskiest first. Keep
item 8 out of that list; it is not blocking the upgrade.

Phase two: one change at a time, and ask before each.

Once I say to proceed, work down that list. For every item:

- show me the exact change you propose, as a diff, before applying it
- say what it fixes and what could go wrong
- wait for my approval on that item before applying it, and before moving on
- if I say no, skip it and continue to the next

Do not batch changes. Do not apply anything I have not approved. If a fix needs
a decision only I can make, such as which replacement endpoint to use, stop and
ask rather than choosing.

Replacements, for reference:

    getUsersFollows        -> ChannelsApi::getChannelFollowers
                              or ::getFollowedChannels
    getHypeTrainEvents     -> HypeTrainApi::getHypeTrainStatus
    replaceStreamTags      -> ChannelsApi::modifyChannelInfo, tags field
    WebhooksApi            -> EventSubApi
    WebhooksSubscriptionApi-> EventSubApi
    the entitlements three -> nothing, Twitch removed the feature

Once the seven are done, offer item 8 separately and say plainly that it is
optional: swapping the deprecated `NewTwitchApi\` imports for `TwitchApi\` is a
find and replace that changes nothing else, and nothing breaks if we skip it.

When every approved item is done, run the test suite and tell me what changed
and what you skipped.
```

</details>

### What 8.0.0 adds that you may want

- Typed exceptions. `TwitchApi\Exception\TwitchApiException` catches anything
  from Twitch, and `RateLimitException::getRetryAfter()` tells you how long to
  wait instead of guessing.
- `TwitchApi\Paginator::items()` walks a cursor-paginated endpoint. The last
  page sends an empty pagination object, which hand-written loops usually get
  wrong.
- `TwitchApi\RateLimit`, built from headers the library used to discard.
- Any PSR-18 client can be injected in place of `HelixGuzzleClient`.
- `OauthApi::revokeToken()` and the device code grant.

See the [changelog](CHANGELOG.md) for the full list.

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

If you are coming from 7.2.0 and want the intermediate step, read
[7.2.0 to 7.3.0](#720-to-730) below. Otherwise go straight to
[7.x to 8.0.0](#7x-to-800).

## 7.2.0 to 7.3.0

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

### What is still broken in 7.3.0

Deliberately, because fixing them needs a major version:

- `WebhooksSubscriptionApi` is fatal when constructed without an explicit
  client, and wraps an endpoint Twitch retired in 2021. Use `EventSubApi`.
- Query parameter values are not URL encoded. A value containing `&`, `+` or
  `#` does not reach Twitch intact, and a caller-supplied value can append
  query parameters of its own to the request. This is fixed in 8.0.0 rather than
  here, because encoding would double-encode anyone who worked around it by
  pre-encoding their values, and that is a breaking change. If you have such a
  workaround, keep it for now; 8.0.0 tells you when to remove it.
- `OauthApi::getAuthUrl()` does not encode its parameters either, so a scope
  list or a redirect URI with a query string produces a malformed URL.
- Six methods call endpoints Twitch has withdrawn. They fail at the API, not in
  this library.

See the [changelog](CHANGELOG.md) for the full list, and
[7.x to 8.0.0](#7x-to-800) for the release that fixes the four above.

