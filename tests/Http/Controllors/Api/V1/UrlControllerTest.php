<?php

namespace Devpri\Tinre\Tests\Http\Controllers\Api\V1;

use Devpri\Tinre\Models\Url;
use Devpri\Tinre\Models\User;
use Devpri\Tinre\Tests\TestCase;

class UrlControllerTest extends TestCase
{
    public function test_cant_get_url()
    {
        $this->json('GET', '/api/v1/urls')
            ->assertStatus(401);
    }

    public function test_can_get_urls()
    {
        $user = factory(User::class)->states('user')->create();
        $accessToken = $user->createToken('test', ['url:view']);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        factory(Url::class, 5)->create();
        factory(Url::class, 5)->create(['user_id' => $user->id]);

        $this->json('GET', '/api/v1/urls')
            ->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_cant_get_urls()
    {
        $user = factory(User::class)->states('user')->create();
        $accessToken = $user->createToken('test', null);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $this->json('GET', '/api/v1/urls')
            ->assertStatus(401);
    }

    public function test_user_can_get_own_url()
    {
        $user = factory(User::class)->states('user')->create();
        $accessToken = $user->createToken('test', ['url:view']);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $url = factory(Url::class)->create(['user_id' => $user->id]);

        $this->json('GET', "/api/v1/urls/{$url->path}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'path' => $url->path,
                ],
            ]);
    }

    public function test_user_cant_get_other_user_url()
    {
        $user = factory(User::class)->states('user')->create();
        $accessToken = $user->createToken('test', ['url:view']);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create(['user_id' => $secondUser->id]);

        $this->json('GET', "/api/v1/urls/{$url->path}")
            ->assertStatus(401);
    }

    public function test_editor_can_get_other_user_url()
    {
        $user = factory(User::class)->states('editor')->create();
        $accessToken = $user->createToken('test', ['url:view:any']);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create(['user_id' => $secondUser->id]);

        $this->json('GET', "/api/v1/urls/{$url->path}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'path' => $url->path,
                ],
            ]);
    }

    public function test_admin_can_get_other_user_url()
    {
        $user = factory(User::class)->states('editor')->create();
        $accessToken = $user->createToken('test', ['url:view:any']);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create(['user_id' => $secondUser->id]);

        $this->json('GET', "/api/v1/urls/{$url->path}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'path' => $url->path,
                ],
            ]);
    }

    public function test_user_can_create_url()
    {
        $user = factory(User::class)->states('user')->create();
        $accessToken = $user->createToken('test', ['url:create']);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $data = [
            'long_url' => 'https://google.com',
        ];

        $this->json('POST', '/api/v1/urls', $data)
            ->assertStatus(201)
            ->assertJson([
                'data' => $data,
            ])
            ->assertJsonFragment([
                'user_id' => $user->id,
            ]);
    }

    public function test_user_without_permission_cant_create_url()
    {
        $user = factory(User::class)->states('user')->create();
        $accessToken = $user->createToken('test', []);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $data = [
            'long_url' => 'https://google.com',
        ];

        $this->json('POST', '/api/v1/urls', $data)
            ->assertStatus(401);
    }

    public function test_user_can_update_own_url()
    {
        $user = factory(User::class)->states('editor')->create();
        $accessToken = $user->createToken('test', ['url:update']);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $url = factory(Url::class)->create(['user_id' => $user->id]);

        $newData = [
            'active' => 0,
            'path' => 'test1233343434',
            'long_url' => 'https://www.google.com',
        ];

        $this->json('post', "/api/v1/urls/{$url->id}", $newData)
            ->assertStatus(200)
            ->assertJsonFragment($newData);
    }

    public function test_user_can_delete_own_url()
    {
        $user = factory(User::class)->states('user')->create();
        $accessToken = $user->createToken('test', ['url:delete']);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $url = factory(Url::class)->create(['user_id' => $user->id]);

        $this->json('delete', "/api/v1/urls/{$url->id}")
            ->assertStatus(200);
    }

    public function test_user_cant_delete_other_user_url()
    {
        $user = factory(User::class)->states('user')->create();
        $accessToken = $user->createToken('test', ['url:delete']);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $secondUser = factory(User::class)->states('user')->create();
        $url = factory(Url::class)->create(['user_id' => $secondUser->id]);

        $this->json('delete', "/api/v1/urls/{$url->id}")
            ->assertStatus(401);
    }

    public function test_editor_can_delete_other_user_url()
    {
        $user = factory(User::class)->states('editor')->create();
        $accessToken = $user->createToken('test', ['url:delete:any']);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $secondUser = factory(User::class)->states('user')->create();
        $url = factory(Url::class)->create(['user_id' => $secondUser->id]);

        $this->json('delete', "/api/v1/urls/{$url->id}")
            ->assertStatus(200);
    }

    public function test_admin_can_delete_other_user_url()
    {
        $user = factory(User::class)->states('administrator')->create();
        $accessToken = $user->createToken('test', ['url:delete:any']);
        $this->actingAs($user->withAccessToken($accessToken), 'api');

        $secondUser = factory(User::class)->states('user')->create();
        $url = factory(Url::class)->create(['user_id' => $secondUser->id]);

        $this->json('delete', "/api/v1/urls/{$url->id}")
            ->assertStatus(200);
    }
}
