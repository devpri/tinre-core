<?php

namespace Devpri\Tinre\Tests\Http\Controllers\Web;

use Devpri\Tinre\Models\AccessToken;
use Devpri\Tinre\Models\User;
use Devpri\Tinre\Tests\TestCase;

class AccessTokenControllerTest extends TestCase
{
    public function test_cant_get_token()
    {
        $this->json('GET', '/web/access-tokens')
            ->assertStatus(401);
    }

    public function test_user_can_get_own_tokens()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $accessToken = $user->createToken('test', ['url:view', 'test']);

        $this->json('GET', '/web/access-tokens')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'name' => $accessToken->name,
                'user_id' => $user->id,
                'permissions' => ['url:view'],
            ]);
    }

    public function test_admin_can_get_all_tokens()
    {
        $user = factory(User::class)->states('administrator')->create();
        $this->actingAs($user);

        $accessToken = $user->createToken('test2', ['url:view', 'test']);

        $secondUser = factory(User::class)->states('user')->create();

        $secondAccessToken = $secondUser->createToken('test2', ['url:view', 'url:view:any']);

        $this->json('GET', '/web/access-tokens')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment([
                'name' => $accessToken->name,
                'user_id' => $user->id,
                'permissions' => ['url:view'],
            ])
            ->assertJsonFragment([
                'name' => $secondAccessToken->name,
                'user_id' => $secondUser->id,
                'permissions' => $secondAccessToken->permissions,
            ]);
    }

    public function test_user_can_get_own_token()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $accessToken = $user->createToken('test', ['url:view', 'test']);

        $this->json('GET', "/web/access-tokens/{$accessToken->id}")
            ->assertStatus(200)
            ->assertJsonFragment([
                'name' => $accessToken->name,
                'user_id' => $user->id,
                'permissions' => ['url:view'],
            ]);
    }

    public function test_user_cant_get_other_user_token()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $accessToken = $secondUser->createToken('test', ['url:view', 'test']);

        $this->json('GET', "/web/access-tokens/{$accessToken->id}")
            ->assertStatus(401);
    }

    public function test_admin_can_get_other_user_token()
    {
        $user = factory(User::class)->states('administrator')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $accessToken = $secondUser->createToken('test', ['url:view']);

        $this->json('GET', "/web/access-tokens/{$accessToken->id}")
            ->assertStatus(200)
            ->assertJsonFragment([
                'name' => $accessToken->name,
                'user_id' => $secondUser->id,
                'permissions' => ['url:view'],
            ]);
    }

    public function test_can_create_token()
    {
        $user = factory(User::class)->states('administrator')->create();
        $this->actingAs($user);

        $this->json('POST', '/web/access-tokens', [
            'name' => 'test',
            'permissions' => ['url:view'],
        ])
            ->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'test',
                'user_id' => $user->id,
                'permissions' => ['url:view'],
            ]);
    }

    public function test_user_can_update_own_token()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $accessToken = $user->createToken('test', ['url:view', 'test']);

        $this->json('POST', "/web/access-tokens/{$accessToken->id}", [
            'name' => 'test2',
            'permissions' => ['url:update'],
        ])
            ->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'test2',
                'user_id' => $user->id,
                'permissions' => ['url:update'],
            ]);
    }

    public function test_user_cant_update_other_user_token()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $accessToken = $secondUser->createToken('test', ['url:view', 'test']);

        $this->json('POST', "/web/access-tokens/{$accessToken->id}", [
            'name' => 'test2',
            'permissions' => ['url:update'],
        ])
            ->assertStatus(401);
    }

    public function test_admin_can_update_user_token()
    {
        $user = factory(User::class)->states('administrator')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $accessToken = $secondUser->createToken('test', ['url:view', 'test']);

        $this->json('POST', "/web/access-tokens/{$accessToken->id}", [
            'name' => 'test2',
            'permissions' => ['url:update'],
        ])
            ->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'test2',
                'user_id' => $secondUser->id,
                'permissions' => ['url:update'],
            ]);
    }

    public function test_user_can_delete_own_token()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $accessToken = $user->createToken('test', ['url:view', 'test']);

        $this->json('delete', "/web/access-tokens/{$accessToken->id}")
            ->assertStatus(200);

        $this->assertTrue(AccessToken::where('id', $accessToken->id)->first() === null);
    }

    public function test_user_cant_delete_other_user_token()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $accessToken = $secondUser->createToken('test', ['url:view', 'test']);

        $this->json('delete', "/web/access-tokens/{$accessToken->id}")
            ->assertStatus(401);
    }

    public function test_admin_can_delete_user_token()
    {
        $user = factory(User::class)->states('administrator')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $accessToken = $secondUser->createToken('test', ['url:view', 'test']);

        $this->json('delete', "/web/access-tokens/{$accessToken->id}")
            ->assertStatus(200);

        $this->assertTrue(AccessToken::where('id', $accessToken->id)->first() === null);
    }
}
