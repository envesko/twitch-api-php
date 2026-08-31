<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\ExtensionsApi;
use TwitchApi\Tests\ResourceTestCase;

class ExtensionsApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return ExtensionsApi::class;
    }

    public function testShouldGetExtensions(): void
    {
        $this->api()->getExtensions('TEST_JWT', 'ext');

        $this->assertSent('GET', 'extensions', [
            ['extension_id', 'ext'],
        ]);
    }

    public function testShouldGetExtensionsForAVersion(): void
    {
        $this->api()->getExtensions('TEST_JWT', 'ext', '1.0.0');

        $this->assertSent('GET', 'extensions', [
            ['extension_id', 'ext'],
            ['extension_version', '1.0.0'],
        ]);
    }

    public function testShouldGetReleasedExtensions(): void
    {
        $this->api()->getReleasedExtensions(self::TOKEN, 'ext');

        $this->assertSent('GET', 'extensions/released', [
            ['extension_id', 'ext'],
        ]);
    }

    public function testShouldGetExtensionLiveChannels(): void
    {
        $this->api()->getExtensionLiveChannels(self::TOKEN, 'ext', 20, 'cursor');

        $this->assertSent('GET', 'extensions/live', [
            ['extension_id', 'ext'],
            ['first', '20'],
            ['after', 'cursor'],
        ]);
    }

    public function testShouldGetAnExtensionConfigurationSegment(): void
    {
        $this->api()->getExtensionConfigurationSegment('TEST_JWT', 'ext', ['broadcaster'], '123');

        $this->assertSent('GET', 'extensions/configurations', [
            ['extension_id', 'ext'],
            ['segment', 'broadcaster'],
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldSetAnExtensionConfigurationSegment(): void
    {
        $this->api()->setExtensionConfigurationSegment('TEST_JWT', 'ext', 'global', ['content' => '{}']);

        $this->assertSent('PUT', 'extensions/configurations');
        $this->assertSentBody(['extension_id' => 'ext', 'segment' => 'global', 'content' => '{}']);
    }

    public function testShouldSetExtensionRequiredConfiguration(): void
    {
        $this->api()->setExtensionRequiredConfiguration('TEST_JWT', '123', 'ext', '1.0.0', 'config');

        $this->assertSent('PUT', 'extensions/required_configuration', [
            ['broadcaster_id', '123'],
        ]);
        $this->assertSentBody(['extension_id' => 'ext', 'extension_version' => '1.0.0', 'required_configuration' => 'config']);
    }

    public function testShouldSendAnExtensionPubsubMessage(): void
    {
        $this->api()->sendExtensionPubSubMessage('TEST_JWT', ['broadcast'], '123', false, 'hello');

        $this->assertSent('POST', 'extensions/pubsub');
        $this->assertSentBody(['target' => ['broadcast'], 'broadcaster_id' => '123', 'is_global_broadcast' => false, 'message' => 'hello']);
    }

    public function testShouldSendAnExtensionChatMessage(): void
    {
        $this->api()->sendExtensionChatMessage('TEST_JWT', '123', 'hello', 'ext', '1.0.0');

        $this->assertSent('POST', 'extensions/chat', [
            ['broadcaster_id', '123'],
        ]);
        $this->assertSentBody(['text' => 'hello', 'extension_id' => 'ext', 'extension_version' => '1.0.0']);
    }

    public function testShouldGetExtensionSecrets(): void
    {
        $this->api()->getExtensionSecrets('TEST_JWT', 'ext');

        $this->assertSent('GET', 'extensions/jwt/secrets', [
            ['extension_id', 'ext'],
        ]);
    }

    public function testShouldCreateAnExtensionSecret(): void
    {
        $this->api()->createExtensionSecret('TEST_JWT', 'ext', 300);

        $this->assertSent('POST', 'extensions/jwt/secrets', [
            ['extension_id', 'ext'],
            ['delay', '300'],
        ]);
    }

    public function testShouldGetReleasedExtensionsForAVersion(): void
    {
        $this->api()->getReleasedExtensions(self::TOKEN, 'ext', '1.0.0');

        $this->assertSent('GET', 'extensions/released', [
            ['extension_id', 'ext'],
            ['extension_version', '1.0.0'],
        ]);
    }
}
