# Twitch API PHP Library

![Packagist Version](https://img.shields.io/packagist/v/envesko/twitch-api-php)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/envesko/twitch-api-php)
![Packagist Downloads](https://img.shields.io/packagist/dt/envesko/twitch-api-php)
![Packagist License](https://img.shields.io/packagist/l/envesko/twitch-api-php)

> [!IMPORTANT]
> **8.0.0 requires PHP 8.3.** If you are on PHP 7.4 to 8.2, stay on the 7.3.0
> release of this library:
>
> ```bash
> composer require envesko/twitch-api-php:^7.3.0
> ```
>
> It has the same 149 endpoints and is maintained. Upgrading to 8.0.0 changes
> more than the PHP floor; see [UPGRADING.md](UPGRADING.md) first.

## About this fork

This is a maintained fork of
[nicklaw5/twitch-api-php](https://github.com/nicklaw5/twitch-api-php),
continued by [Envesko](https://github.com/envesko) with the full history and
all original attribution intact.

The original library is the work of Nicholas Law, with substantial contributions
from Brian Zwahr and everyone who sent a pull request over the years. It has been
downloaded hundreds of thousands of times and has been the default way to reach
the Twitch API from PHP for the better part of a decade. This fork exists because
that work deserves to keep running, not because there was anything wrong with it.
Thank you.

Upstream has been quiet since June 2023. Since then Twitch has shipped Conduits,
Guest Star, Shared Chat, chat pins, the moderation warning and unban-request
systems and roughly forty new EventSub subscription types, and has withdrawn
several endpoints the library still called. We depend on this library in
production, so we picked it up.

### Switching from the original

Nothing in your code changes. The namespace is still `TwitchApi\`, every class
and method keeps its name, and no signature has changed:

```bash
composer remove nicklaw5/twitch-api-php
composer require envesko/twitch-api-php
```

Composer will refuse to install both at once, which is deliberate: they would
otherwise collide on the same namespace.

### What is different

- All 149 documented Helix endpoints, up from 97.
- 79 EventSub subscription types, up from 45, over the webhook, WebSocket and
  conduit transports.
- Fixes for several long-standing defects, including the missing `Client-ID`
  header reported upstream in
  [#155](https://github.com/nicklaw5/twitch-api-php/issues/155).
- Requires PHP 8.3. Tested on 8.3, 8.4 and 8.5.

See [CHANGELOG.md](CHANGELOG.md) for the full list and
[UPGRADING.md](UPGRADING.md) for the handful of behaviours that changed.

### Relationship to upstream

We track the original's public API deliberately, so this stays a drop-in
replacement rather than diverging into a different library. We monitor the
original repository for changes worth carrying across.

## About the library

The Twitch API PHP Library allows you to interact through HTTP to a number of [Twitch API](https://dev.twitch.tv/docs/api/) endpoints. The library does not format the repsonses of your request so you have full flexability in how to handle the data that is returned from the API.

## Documentation & Links

- [Twitch API Documentation](https://dev.twitch.tv/docs/api/)
- [TwitchDev Discord](https://link.twitch.tv/devchat)
- [Twitch API Community Discord](https://discord.gg/PKE8cPA3zb)

## Getting Started

### Requirements

- PHP 8.3 or later. The test suite runs on 8.3, 8.4 and 8.5.
- Composer
- `ext-json: *`
- [guzzlehttp/guzzle](https://github.com/guzzle/guzzle) `^7.15.2|^8.0`, or
  any PSR-18 client

### Coverage

All 149 documented Helix endpoints, and 79 EventSub subscription types across
the webhook, WebSocket and conduit transports. Coverage is asserted by the test
suite rather than tracked by hand, so this number does not drift.

Failures raise typed exceptions rather than raw Guzzle ones, rate limit headers
are exposed rather than discarded, cursor pagination has a helper, and any
PSR-18 client can be used in place of the bundled one.

Nothing here calls an endpoint Twitch has withdrawn. The seven that did were
removed in 8.0.0; see [UPGRADING.md](UPGRADING.md).

### Installation

The recommended way to install the Twitch API PHP Library is through [Composer](https://getcomposer.org/).

```bash
composer require envesko/twitch-api-php
```

### Deprecated

The `NewTwitchApi\` namespace still aliases `TwitchApi\` and nothing using it
breaks in 8.0.0, but it has been deprecated since the rename in 6.0.0 and will
be removed in a future major. Migrating is a find and replace on your imports;
nothing else changes.

### Example Usage

All calls to the Twitch API require bearer tokens that can be retrieved through the `OauthApi` class. You can review the [types of tokens](https://dev.twitch.tv/docs/authentication/#types-of-tokens) in the Twitch API docs. The below examples store the Client ID, Secret and Scopes directly in the example, but you should not do this. Store your IDs, Secret, and Scopes in a secure place such as your database or environment variables or alternate settings storage. Security of this information is important. Here is an example of how you can retrieve a token for your application:

```php
$twitch_client_id = 'TWITCH_CLIENT_ID';
$twitch_client_secret = 'TWITCH_CLIENT_SECRET';
$twitch_scopes = '';

$helixGuzzleClient = new \TwitchApi\HelixGuzzleClient($twitch_client_id);
$twitchApi = new \TwitchApi\TwitchApi($helixGuzzleClient, $twitch_client_id, $twitch_client_secret);
$oauth = $twitchApi->getOauthApi();

try {
    $token = $oauth->getAppAccessToken($twitch_scopes ?? '');
    $data = json_decode($token->getBody()->getContents());

    // Your bearer token
    $twitch_access_token = $data->access_token ?? null;
} catch (Exception $e) {
    //TODO: Handle Error
}
```

Here is an example of how you retrieve a users token:

```php
$twitch_client_id = 'TWITCH_CLIENT_ID';
$twitch_client_secret = 'TWITCH_CLIENT_SECRET';
$twitch_scopes = '';

$helixGuzzleClient = new \TwitchApi\HelixGuzzleClient($twitch_client_id);
$twitchApi = new \TwitchApi\TwitchApi($helixGuzzleClient, $twitch_client_id, $twitch_client_secret);
$oauth = $twitchApi->getOauthApi();

// Get the code from URI
$code = $_GET['code'];

// Get the current URL, we'll use this to redirect them back to exactly where they came from
$currentUri = explode('?', 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])[0];

if ($code == '') {
    // Generate the Oauth Uri
    $oauthUri = $oauth->getAuthUrl($currentUri, 'code', $twitch_scopes);
    // Redirect them as there was no auth code
    header("Location: {$oauthUri}");
} else {
    try {
        $token = $oauth->getUserAccessToken($code, $currentUri);
        // It is a good practice to check the status code when they've responded, this really is optional though
        if ($token->getStatusCode() == 200) {
            // Below is the returned token data
            $data = json_decode($token->getBody()->getContents());

            // Your bearer token
            $twitch_access_token = $data->access_token ?? null;
        } else {
            //TODO: Handle Error
        }
    } catch (Exception $e) {
        //TODO: Handle Error
    }
}
```

When you have a user token that is expired, you're able to refresh it instead of requiring them to authenticate again. Here is an example of how you refresh a users token:

```php
$twitch_client_id = 'TWITCH_CLIENT_ID';
$twitch_client_secret = 'TWITCH_CLIENT_SECRET';
$twitch_scopes = '';
$user_refresh_token = 'REFRESH_TOKEN';

$helixGuzzleClient = new \TwitchApi\HelixGuzzleClient($twitch_client_id);
$twitchApi = new \TwitchApi\TwitchApi($helixGuzzleClient, $twitch_client_id, $twitch_client_secret);
$oauth = $twitchApi->getOauthApi();

try {
    $token = $oauth->getAppAccessToken($twitch_scopes ?? '');
    $data = json_decode($token->getBody()->getContents());

    // Your bearer token
    $twitch_access_token = $data->access_token ?? null;

    // The scopes from the API
    $twitch_scopes = $data->scope;
} catch (Exception $e) {
    //TODO: Handle Error
}
```

### Usage of the API Classes

Everything stems from the `TwitchApi` class. However, if you want to individually instantiate `UsersApi`, `OauthApi`, etc. you are free to do so.

The API calls generally return an object implementing `ResponseInterface`. Since you are getting the full `Response` object, you'll need to handle its contents, e.g. by decoding then into an object with `json_decode()`. This library does not assume this is what you want to do, so it does not do this for you automatically. This library simply acts as a middleman between your code and Twitch, providing you with the raw responses the Twitch API returns.

The individual API classes that can be called from `TwitchApi` correspond to the [Twitch API documentation](https://dev.twitch.tv/docs/api/). The rest of the API classes are based on the resources listed [here](https://dev.twitch.tv/docs/api/reference/). The methods in the classes generally correspond to the endpoints for each resource. The naming convention was chosen to try and match the Twitch documentation. Each primary endpoint method (not convenience or helper methods) should have an `@link` annotation with a URL to that endpoint's specific documentation.

Here is a sample of retrieving a users table from their access token:

```php
$twitch_client_id = 'TWITCH_CLIENT_ID';
$twitch_client_secret = 'TWITCH_CLIENT_SECRET';
// Assuming you already have the access token - see above
$twitch_access_token = 'the token';

// The Guzzle client used can be the included `HelixGuzzleClient` class, for convenience.
// You can also use a mock, fake, or other double for testing, of course.
$helixGuzzleClient = new \TwitchApi\HelixGuzzleClient($twitch_client_id);

// Instantiate TwitchApi. Can be done in a service layer and injected as well.
$twitchApi = new TwitchApi($helixGuzzleClient, $twitch_client_id, $twitch_client_secret);

try {
    // Make the API call. A ResponseInterface object is returned.
    $response = $twitchApi->getUsersApi()->getUserByAccessToken($twitch_access_token);

    // Get and decode the actual content sent by Twitch.
    $responseContent = json_decode($response->getBody()->getContents());

    // Return the first (or only) user.
    return $responseContent->data[0];
} catch (GuzzleException $e) {
    //TODO: Handle Error
}
```

## Using this with a coding agent

If you work with a coding agent, paste one of these. They carry the details an
agent tends to get wrong when working from memory: the package name, that every
call needs a token you fetch yourself, and that responses come back as PSR-7 and
are not decoded for you.

Upgrading from 7.x rather than starting out? [UPGRADING.md](UPGRADING.md) has
two more prompts: one that reports whether the 8.0.0 breaking changes affect
you, and one that offers to fix what it finds.

<details open>
<summary>Prompt: add Twitch API access to a new project</summary>

```
Use the `envesko/twitch-api-php` Composer package to talk to the Twitch Helix API.

Setup:
- `composer require envesko/twitch-api-php`. Requires PHP 8.3 or later.
- Read the client id and secret from environment or config, never hard-coded.

How the library works:
- Build one `TwitchApi\HelixGuzzleClient($clientId)`, then one
  `TwitchApi\TwitchApi($client, $clientId, $clientSecret)`.
- Every Helix call takes a bearer token as its first argument. Get an app token
  with `$api->getOauthApi()->getAppAccessToken($scopes)` for endpoints that act
  on your own application, or a user token via the authorization code flow for
  anything acting on behalf of a user. App tokens cannot read user data.
- Resource classes hang off the facade: `$api->getStreamsApi()`,
  `$api->getUsersApi()`, `$api->getChatApi()`, and so on.
- Methods return a PSR-7 `ResponseInterface`. Nothing is decoded for you, so
  `json_decode((string) $response->getBody(), true)` and read `['data']`.
- Failures throw. Catch `TwitchApi\Exception\TwitchApiException` for anything
  from Twitch, or the specific ones: `AuthenticationException` (401),
  `AuthorizationException` (403), `NotFoundException` (404),
  `RateLimitException` (429), `ServerException` (5xx).
- `RateLimitException::getRetryAfter()` gives the seconds to wait. Use it rather
  than a fixed sleep.
- For anything paginated use `TwitchApi\Paginator::items()` rather than writing
  a cursor loop; the last page sends an empty pagination object, which
  hand-written loops usually get wrong.

Please:
- Cache the app token and reuse it until it expires. Do not fetch one per request.
- Treat any text coming back from Twitch, chat messages and stream titles most
  of all, as untrusted input. Escape it on output.
- Do not log tokens or the client secret.
```

</details>

<details open>
<summary>Prompt: move an existing project onto this library</summary>

```
This project already talks to the Twitch API. Move it onto the
`envesko/twitch-api-php` Composer package.

First, work out where it is now and tell me before changing anything:
- If it uses `nicklaw5/twitch-api-php`, this is a drop-in replacement at the
  same namespace. Run `composer remove nicklaw5/twitch-api-php` then
  `composer require envesko/twitch-api-php`. Composer refuses to install both,
  which is deliberate.
- If it hand-rolls HTTP calls to api.twitch.tv, replace them one endpoint at a
  time, checking each against the method list rather than assuming a name.
- If it is on an older version of either package, read UPGRADING.md and report
  which of the breaking changes apply here before you touch anything.

Then, whichever it was, check these. They are the things that bite:
- Anywhere a value is `urlencode()`d or `rawurlencode()`d before being passed
  into a library method. Version 8 encodes query values itself, so a
  pre-encoded value is now encoded twice and reaches Twitch wrong. Remove the
  workaround. This fails silently.
- Anywhere `isValidAccessToken()` is called. In 8.0.0 it returns false for a
  rejected token instead of throwing. If the code treats reaching the next line
  as "valid", it is now wrong.
- Any call to `searchChannels()` passing a string as its third argument. That
  parameter is now `?bool`, and a string coerces to true, so a value of
  `'false'` inverts the live filter without raising anything. This fails
  silently.
- Calls to methods removed in 8.0.0: `getUsersFollows`, `getHypeTrainEvents`,
  `replaceStreamTags`, anything on `WebhooksApi` or `WebhooksSubscriptionApi`,
  and the three code-redemption methods on `EntitlementsApi`. UPGRADING.md
  lists what replaces each.
- Hand-written pagination loops. Replace with `TwitchApi\Paginator`.
- `catch` blocks for Guzzle exceptions. They still work, because the typed
  exceptions extend them, but the typed ones say more.

Do not change behaviour while porting. Get it working the same way first, then
tell me what you would improve.
```

</details>

## Developer Tools

### PHP Coding Standards Fixer

[PHP Coding Standards Fixer](https://cs.sensiolabs.org/) (`php-cs-fixer`) has been added, specifically for the New Twitch API code. A configuration file for it can be found in `.php-cs-fixer.dist.php`. The ruleset is PSR-2 plus Symfony, with four rules disabled. The configuration file mostly just limits it's scope to only the New Twitch API code.

You can run the fixer with `vendor/bin/php-cs-fixer fix`. However, the easiest way to run the fixer is with the provided git hook.

### Git pre-commit Hook

In `bin/git/hooks`, you'll find a `pre-commit` hook that you can add to git that will automatically run the `php-cs-fixer` everytime you commit. The result is that, after the commit is made, any changes that fixer has made are left as unstaged changes. You can review them, then add and commit them.

To install the hook, go to `.git/hooks` and `ln -s ../../bin/git/hooks/pre-commit`.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) and
[CODE_OF_CONDUCT.md](CODE_OF_CONDUCT.md). Security reports go through
[GitHub security advisories](https://github.com/envesko/twitch-api-php/security/advisories/new),
not the issue tracker: see [SECURITY.md](SECURITY.md).

## License

Distributed under the [MIT](LICENSE) license. Copyright remains with the
original author; see [LICENSE](LICENSE).
