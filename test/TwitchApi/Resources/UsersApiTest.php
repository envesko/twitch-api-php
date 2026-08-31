<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\UsersApi;
use TwitchApi\Tests\ResourceTestCase;

class UsersApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return UsersApi::class;
    }

    public function testShouldGetUserWithAccessToken(): void
    {
        $this->api()->getUsers(self::TOKEN);

        $this->assertSent('GET', 'users');
    }

    public function testShouldGetUserWithAccessTokenConvenienceMethod(): void
    {
        $this->api()->getUserByAccessToken(self::TOKEN);

        $this->assertSent('GET', 'users');
    }

    public function testShouldGetUsersByIds(): void
    {
        $this->api()->getUsers(self::TOKEN, ['12345', '98765']);

        $this->assertSent('GET', 'users', [
            ['id', '12345'],
            ['id', '98765'],
        ]);
    }

    public function testShouldGetUsersByUsernames(): void
    {
        $this->api()->getUsers(self::TOKEN, [], ['twitchuser', 'anotheruser']);

        $this->assertSent('GET', 'users', [
            ['login', 'twitchuser'],
            ['login', 'anotheruser'],
        ]);
    }

    public function testShouldGetUsersByIdAndUsername(): void
    {
        $this->api()->getUsers(self::TOKEN, ['12345', '98765'], ['twitchuser', 'anotheruser']);

        $this->assertSent('GET', 'users', [
            ['id', '12345'],
            ['id', '98765'],
            ['login', 'twitchuser'],
            ['login', 'anotheruser'],
        ]);
    }

    public function testShouldGetASingleUserById(): void
    {
        $this->api()->getUserById(self::TOKEN, '12345');

        $this->assertSent('GET', 'users', [
            ['id', '12345'],
        ]);
    }

    public function testShouldGetASingleUserByUsername(): void
    {
        $this->api()->getUserByUsername(self::TOKEN, 'twitchuser');

        $this->assertSent('GET', 'users', [
            ['login', 'twitchuser'],
        ]);
    }

    public function testShouldUpdateUser(): void
    {
        $this->api()->updateUser(self::TOKEN);

        $this->assertSent('PUT', 'users');
    }

    public function testShouldUpdateUserDescription(): void
    {
        $this->api()->updateUser(self::TOKEN, 'test');

        $this->assertSent('PUT', 'users', [
            ['description', 'test'],
        ]);
    }

    public function testShouldGetUserBlockList(): void
    {
        $this->api()->getUserBlockList(self::TOKEN, '123');

        $this->assertSent('GET', 'users/blocks', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetUserBlockListWithOpts(): void
    {
        $this->api()->getUserBlockList(self::TOKEN, '123', 100, 'abc');

        $this->assertSent('GET', 'users/blocks', [
            ['broadcaster_id', '123'],
            ['first', '100'],
            ['after', 'abc'],
        ]);
    }

    public function testShouldBlockUser(): void
    {
        $this->api()->blockUser(self::TOKEN, '123');

        $this->assertSent('PUT', 'users/blocks', [
            ['target_user_id', '123'],
        ]);
    }

    public function testShouldBlockUserWithOpts(): void
    {
        $this->api()->blockUser(self::TOKEN, '123', 'chat', 'spam');

        $this->assertSent('PUT', 'users/blocks', [
            ['target_user_id', '123'],
            ['source_context', 'chat'],
            ['reason', 'spam'],
        ]);
    }

    public function testShouldUnblockUser(): void
    {
        $this->api()->unblockUser(self::TOKEN, '123');

        $this->assertSent('DELETE', 'users/blocks', [
            ['target_user_id', '123'],
        ]);
    }

    public function testShouldUpdateUserExtensions(): void
    {
        $data = ['panel' => ['1' => ['active' => true, 'id' => 'ext', 'version' => '1.0.0']]];

        $this->api()->updateUserExtensions(self::TOKEN, $data);

        $this->assertSent('PUT', 'users/extensions');
        $this->assertSentBody(['data' => $data]);
    }

    public function testShouldGetAuthorizationByUser(): void
    {
        $this->api()->getAuthorizationByUser(self::TOKEN, '456');

        $this->assertSent('GET', 'authorization/users', [
            ['user_id', '456'],
        ]);
    }

    public function testShouldGetUserExtensions(): void
    {
        $this->api()->getUserExtensions(self::TOKEN);

        $this->assertSent('GET', 'users/extensions/list');
    }

    public function testShouldGetActiveUserExtensions(): void
    {
        $this->api()->getActiveUserExtensions(self::TOKEN);

        $this->assertSent('GET', 'users/extensions');
    }

    public function testShouldGetActiveUserExtensionsForAnotherUser(): void
    {
        $this->api()->getActiveUserExtensions(self::TOKEN, '456');

        $this->assertSent('GET', 'users/extensions', [
            ['user_id', '456'],
        ]);
    }

    public function testShouldGetUsersIncludingEmail(): void
    {
        $this->api()->getUsers(self::TOKEN, ['123'], [], true);

        $this->assertSent('GET', 'users', [
            ['id', '123'],
            ['scope', 'user:read:email'],
        ]);
    }
}
