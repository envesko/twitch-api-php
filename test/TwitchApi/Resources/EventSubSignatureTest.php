<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use PHPUnit\Framework\TestCase;
use TwitchApi\HelixGuzzleClient;
use TwitchApi\RequestGenerator;
use TwitchApi\Resources\EventSubApi;

/**
 * Signature verification decides whether an inbound webhook is really from Twitch, so it is
 * the one method in the library where a wrong answer is a security problem rather than a bug.
 */
class EventSubSignatureTest extends TestCase
{
    private const SECRET = 'a-shared-secret';
    private const MESSAGE_ID = 'e76c6bd4-55c9-4987-8304-da1588d8988b';
    private const TIMESTAMP = '2026-08-30T00:00:00.000000000Z';
    private const BODY = '{"subscription":{"id":"1"},"event":{}}';

    private function api(): EventSubApi
    {
        return new EventSubApi(new HelixGuzzleClient('TEST_CLIENT_ID'), new RequestGenerator());
    }

    private function sign(string $algo = 'sha256', string $secret = self::SECRET): string
    {
        return $algo.'='.hash_hmac($algo, self::MESSAGE_ID.self::TIMESTAMP.self::BODY, $secret);
    }

    private function verify(string $signature): bool
    {
        return $this->api()->verifySignature($signature, self::SECRET, self::MESSAGE_ID, self::TIMESTAMP, self::BODY);
    }

    public function testAGenuineSignatureIsAccepted(): void
    {
        $this->assertTrue($this->verify($this->sign()));
    }

    public function testASignatureMadeWithTheWrongSecretIsRejected(): void
    {
        $this->assertFalse($this->verify($this->sign('sha256', 'not-the-secret')));
    }

    public function testATamperedHashIsRejected(): void
    {
        $this->assertFalse($this->verify('sha256='.str_repeat('0', 64)));
    }

    public function testABodyChangeInvalidatesTheSignature(): void
    {
        $signature = $this->sign();
        $api = $this->api();

        $this->assertFalse($api->verifySignature(
            $signature,
            self::SECRET,
            self::MESSAGE_ID,
            self::TIMESTAMP,
            '{"subscription":{"id":"1"},"event":{"tampered":true}}'
        ));
    }

    public function testAReplayedMessageIdInvalidatesTheSignature(): void
    {
        $signature = $this->sign();
        $api = $this->api();

        $this->assertFalse($api->verifySignature(
            $signature,
            self::SECRET,
            'a-different-message-id',
            self::TIMESTAMP,
            self::BODY
        ));
    }

    /**
     * @dataProvider malformedSignatures
     */
    public function testAMalformedSignatureIsRejectedRatherThanFatal(string $signature): void
    {
        // Each of these previously reached explode() and left the hash element undefined,
        // raising a warning. Verification still returned false, but under an error handler
        // that promotes warnings to exceptions the handler crashed on an unauthenticated request.
        $this->assertFalse($this->verify($signature));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function malformedSignatures(): array
    {
        return [
            'empty' => [''],
            'no separator' => ['sha256'],
            'hash only' => [str_repeat('0', 64)],
            'unknown algorithm' => ['definitely-not-an-algorithm='.str_repeat('0', 64)],
            'empty algorithm' => ['='.str_repeat('0', 64)],
        ];
    }

    public function testAnAlgorithmWithASeparatorInTheHashStillParses(): void
    {
        // explode() is limited to two parts, so a base64-ish hash containing = is not truncated.
        $this->assertFalse($this->verify('sha256=abc=def'));
    }
}
