<?php

declare(strict_types=1);

namespace TwitchApi\Tests;

use PHPUnit\Framework\TestCase;
use TwitchApi\RequestGenerator;

/**
 * Every request the library sends is built here, but until now nothing asserted the result.
 * The resource specs mock this class away and assert the parameter map they hand it, so a
 * defect in turning that map into a URL was invisible to a fully green suite.
 */
class RequestGeneratorTest extends TestCase
{
    private function uri(array $queryParamsMap, array $bodyParams = []): string
    {
        return (string) (new RequestGenerator())
            ->generate('GET', 'games', 'TEST_TOKEN', $queryParamsMap, $bodyParams)
            ->getUri();
    }

    // ------------------------------------------------------------------ query strings

    public function testNoParametersProducesNoQueryString(): void
    {
        $this->assertSame('games', $this->uri([]));
    }

    public function testSingleParameterIsAppended(): void
    {
        $this->assertSame('games?id=123', $this->uri([['key' => 'id', 'value' => '123']]));
    }

    public function testRepeatedKeysAreBothKept(): void
    {
        // Twitch uses repeated keys rather than array syntax for multi-value filters.
        $this->assertSame('games?id=1&id=2', $this->uri([
            ['key' => 'id', 'value' => '1'],
            ['key' => 'id', 'value' => '2'],
        ]));
    }

    public function testNullValuesAreOmitted(): void
    {
        $this->assertSame('games?after=x', $this->uri([
            ['key' => 'first', 'value' => null],
            ['key' => 'after', 'value' => 'x'],
        ]));
    }

    public function testIntegerValueIsRendered(): void
    {
        $this->assertSame('games?first=100', $this->uri([['key' => 'first', 'value' => 100]]));
    }

    public function testZeroIsKeptRatherThanTreatedAsAbsent(): void
    {
        $this->assertSame('games?first=0', $this->uri([['key' => 'first', 'value' => 0]]));
    }

    public function testBooleansBecomeOneAndZero(): void
    {
        $this->assertSame('games?live=1', $this->uri([['key' => 'live', 'value' => true]]));
        $this->assertSame('games?live=0', $this->uri([['key' => 'live', 'value' => false]]));
    }

    // ------------------------------------------------------------------ encoding

    public function testSpacesAreEncoded(): void
    {
        $this->assertSame(
            'games?name=Grand%20Theft%20Auto%20V',
            $this->uri([['key' => 'name', 'value' => 'Grand Theft Auto V']])
        );
    }

    public function testAmpersandInAValueDoesNotStartANewParameter(): void
    {
        // Unencoded this reads as name=Rock, an empty parameter, then Roll.
        $this->assertSame(
            'games?name=Rock%20%26%20Roll',
            $this->uri([['key' => 'name', 'value' => 'Rock & Roll']])
        );
    }

    public function testPlusInAValueSurvives(): void
    {
        // Twitch pagination cursors are base64 and routinely contain + and =. Unencoded, the
        // + is decoded back as a space and the cursor is rejected.
        $this->assertSame(
            'games?after=eyJiIjpudWxsL%2B8%3D',
            $this->uri([['key' => 'after', 'value' => 'eyJiIjpudWxsL+8=']])
        );
    }

    public function testHashInAValueDoesNotBecomeAFragment(): void
    {
        // Unencoded, everything after the # is dropped from the query entirely.
        $this->assertSame(
            'games?name=C%23%20tutorial',
            $this->uri([['key' => 'name', 'value' => 'C# tutorial']])
        );
    }

    public function testEqualsInAValueIsEncoded(): void
    {
        $this->assertSame('games?name=a%3Db', $this->uri([['key' => 'name', 'value' => 'a=b']]));
    }

    public function testAValueCannotInjectAdditionalParameters(): void
    {
        // Search terms are user input. Without encoding, a caller's user could append their
        // own query parameters to the outgoing Twitch request.
        $this->assertSame(
            'games?name=x%26first%3D100%26after%3Devil',
            $this->uri([['key' => 'name', 'value' => 'x&first=100&after=evil']])
        );
    }

    public function testNonAsciiIsEncoded(): void
    {
        $this->assertSame('games?name=caf%C3%A9', $this->uri([['key' => 'name', 'value' => 'café']]));
    }

    // ------------------------------------------------------------------ headers

    public function testAcceptHeaderIsAlwaysSent(): void
    {
        $request = (new RequestGenerator())->generate('GET', 'games', 'TEST_TOKEN');

        $this->assertSame(['application/json'], $request->getHeader('Accept'));
    }

    public function testBearerTokenBecomesAnAuthorizationHeader(): void
    {
        $request = (new RequestGenerator())->generate('GET', 'games', 'TEST_TOKEN');

        $this->assertSame(['Bearer TEST_TOKEN'], $request->getHeader('Authorization'));
    }

    public function testNoAuthorizationHeaderWithoutABearer(): void
    {
        // Get Channel iCalendar is public, so the library sends no bearer for it.
        $request = (new RequestGenerator())->generate('GET', 'schedule/icalendar', null);

        $this->assertFalse($request->hasHeader('Authorization'));
    }

    public function testHttpMethodAndEndpointArePassedThrough(): void
    {
        $request = (new RequestGenerator())->generate('POST', 'eventsub/subscriptions', 'TEST_TOKEN');

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('eventsub/subscriptions', (string) $request->getUri());
    }

    // ------------------------------------------------------------------ bodies

    public function testBodyParamsAreJsonEncoded(): void
    {
        $request = (new RequestGenerator())
            ->generate('POST', 'games', 'TEST_TOKEN', [], [['key' => 'title', 'value' => 'hello']]);

        $this->assertSame('{"title":"hello"}', (string) $request->getBody());
    }

    public function testNullBodyParamsAreOmitted(): void
    {
        $request = (new RequestGenerator())->generate('POST', 'games', 'TEST_TOKEN', [], [
            ['key' => 'title', 'value' => null],
            ['key' => 'length', 'value' => 60],
        ]);

        $this->assertSame('{"length":60}', (string) $request->getBody());
    }

    public function testNestedBodyParamsSurviveEncoding(): void
    {
        // EventSub conditions are nested objects rather than scalars.
        $request = (new RequestGenerator())->generate('POST', 'eventsub/subscriptions', 'TEST_TOKEN', [], [
            ['key' => 'condition', 'value' => ['broadcaster_user_id' => '1234']],
        ]);

        $this->assertSame('{"condition":{"broadcaster_user_id":"1234"}}', (string) $request->getBody());
    }

    public function testBodyAndQueryCanBeSentTogether(): void
    {
        $request = (new RequestGenerator())->generate('PATCH', 'games', 'TEST_TOKEN', [
            ['key' => 'broadcaster_id', 'value' => '1234'],
        ], [
            ['key' => 'title', 'value' => 'new title'],
        ]);

        $this->assertSame('games?broadcaster_id=1234', (string) $request->getUri());
        $this->assertSame('{"title":"new title"}', (string) $request->getBody());
    }

    public function testEmptyBodyParamsProduceNoBody(): void
    {
        $request = (new RequestGenerator())->generate('GET', 'games', 'TEST_TOKEN');

        $this->assertSame('', (string) $request->getBody());
    }
}
