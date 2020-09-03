<?php

namespace Devpri\Tinre\Tests\Traits;

use Devpri\Tinre\Models\User;
use Devpri\Tinre\Tests\TestCase;

class HasApiTokensTest extends TestCase
{
    public function test_can_get_tokens()
    {
        $user = factory(User::class)->states('user')->create();

        $accessToken = $user->createToken('test', ['url:view']);

        $this->assertTrue(count($user->tokens) === 1);
        $this->assertTrue($user->tokens->first()->id === $accessToken->id);
    }

    public function test_can_create_token()
    {
        $user = factory(User::class)->states('user')->create();

        $accessToken = $user->createToken('test', ['url:view']);

        $this->assertTrue($accessToken->name === 'test');
        $this->assertTrue(count($accessToken->permissions) === 1);
        $this->assertTrue(array_diff(['url:view'], $accessToken->permissions) === []);
    }

    public function test_can_assing_token()
    {
        $user = factory(User::class)->states('user')->create();

        $accessToken = $user->createToken('test', ['url:view']);

        $this->assertTrue($user->withAccessToken($accessToken)->accessToken->id === $accessToken->id);
    }

    public function test_can_access_api()
    {
        $user = factory(User::class)->states('user')->create();

        $this->assertTrue($user->hasApiAccess());
    }

    public function test_cant_access_api()
    {
        $user = factory(User::class)->states('user')->create();
        $user->role = 'test';

        $this->assertFalse($user->hasApiAccess());
    }
}
