<?php

namespace Devpri\Tinre\Tests\Http\Controllers\Api\V1;

use Devpri\Tinre\Models\User;
use Devpri\Tinre\Tests\TestCase;

class LoggedUserControllerTest extends TestCase
{
    public function test_cant_get_user()
    {
        $this->json('GET', '/api/v1/users/me')
            ->assertStatus(401);
    }

    public function test_can_get_user()
    {
        $user = factory(User::class)->states('user')->create();
        $accessToken = $user->createToken('test', ['user:view']);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $this->json('GET', '/api/v1/users/me')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ]);
    }

    public function test_cant_get_user_without_token_permissions()
    {
        $user = factory(User::class)->states('user')->create();
        $accessToken = $user->createToken('test', []);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $this->json('GET', '/api/v1/users/me')
            ->assertStatus(401);
    }
}
