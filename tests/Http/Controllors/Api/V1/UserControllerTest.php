<?php

namespace Devpri\Tinre\Tests\Http\Controllers\Api\V1;

use Devpri\Tinre\Models\User;
use Devpri\Tinre\Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function test_guest_cant_get_users()
    {
        $this->json('GET', '/api/v1/users')
            ->assertStatus(401);
    }

    public function test_user_cant_get_users()
    {
        $user = factory(User::class)->states('user')->create();
        $accessToken = $user->createToken('test', ['user:view:any']);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $this->json('GET', '/api/v1/users')
            ->assertStatus(401);
    }

    public function test_editor_cant_get_users()
    {
        $user = factory(User::class)->states('editor')->create();
        $accessToken = $user->createToken('test', ['user:view:any']);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $this->json('GET', '/api/v1/users')
            ->assertStatus(401);
    }

    public function test_admin_can_get_users()
    {
        $user = factory(User::class)->states('administrator')->create();
        $accessToken = $user->createToken('test', ['user:view:any']);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $this->json('GET', '/api/v1/users')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'email' => $user->email,
            ]);
    }

    public function test_admin_can_serach_users()
    {
        $user = factory(User::class)->states('administrator')->create();
        $accessToken = $user->createToken('test', ['user:view:any']);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        factory(User::class, 10)->states('user')->create();

        $this->json('GET', "/api/v1/users/{$user->id}", [
            'search' => $user->email,
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => $user->email,
                ],
            ]);
    }

    public function test_cant_get_user()
    {
        $user = factory(User::class)->states('user')->create();
        $accessToken = $user->createToken('test', ['user:view']);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $secondUser = factory(User::class)->states('user')->create();

        $this->json('GET', "/api/v1/users/{$secondUser->id}")
            ->assertStatus(401);
    }

    public function test_user_can_get_own_user()
    {
        $user = factory(User::class)->states('user')->create();
        $accessToken = $user->createToken('test', ['user:view']);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $this->json('GET', "/api/v1/users/{$user->id}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => $user->email,
                ],
            ]);
    }

    public function test_admin_can_get_user()
    {
        $user = factory(User::class)->states('administrator')->create();
        $accessToken = $user->createToken('test', ['user:view:any']);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $this->json('GET', "/api/v1/users/{$user->id}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => $user->email,
                ],
            ]);
    }
}
