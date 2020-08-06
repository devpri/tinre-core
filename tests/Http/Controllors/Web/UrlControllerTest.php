<?php

namespace Devpri\Tinre\Tests\Http\Controllers\Web;

use Carbon\Carbon;
use Devpri\Tinre\Models\Url;
use Devpri\Tinre\Models\User;
use Devpri\Tinre\Tests\TestCase;

class UrlControllerTest extends TestCase
{

    public function test_cant_get_url()
    {
        $this->json('GET', '/web/urls')
            ->assertStatus(401);
    }

    public function test_can_get_urls()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        factory(Url::class, 5)->create(['user_id' => $user->id]);

        $this->json('GET', '/web/urls')
            ->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_can_search_urls()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        factory(Url::class)->create(['user_id' => $user->id, 'path' => 'test10001']);
        factory(Url::class, 5)->create(['user_id' => $user->id]);

        $this->json('GET', '/web/urls', [
            'search' => 'test10001'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [[
                    'path' => 'test10001'
                ]]
            ]);
    }

    public function test_can_filter_urls_by_date()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        factory(Url::class)->create(['user_id' => $user->id, 'path' => 'test10001']);
        factory(Url::class, 5)->create(['user_id' => $user->id]);

        $this->json('GET', '/web/urls', [
            'date' => [Carbon::today()->subDays(2), Carbon::today()->addDays(2)]
        ])->assertStatus(200)
            ->assertJson([
                'data' => [[
                    'path' => 'test10001'
                ]]
            ]);
    }

    public function test_user_get_own_urls()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        factory(Url::class, 5)->create(['user_id' => $user->id]);

        $secondUser = factory(User::class)->states('user')->create();
        factory(Url::class, 2)->create(['user_id' => $secondUser->id]);

        $this->json('GET', '/web/urls')
            ->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_editor_get_all_urls()
    {
        $user = factory(User::class)->states('editor')->create();
        $this->actingAs($user);

        factory(Url::class, 5)->create(['user_id' => $user->id]);

        $secondUser = factory(User::class)->states('user')->create();
        factory(Url::class, 2)->create(['user_id' => $secondUser->id]);

        $this->json('GET', '/web/urls')
            ->assertStatus(200)
            ->assertJsonCount(7, 'data');
    }

    public function test_admin_get_all_urls()
    {
        $user = factory(User::class)->states('administrator')->create();
        $this->actingAs($user);

        factory(Url::class, 5)->create(['user_id' => $user->id]);

        $secondUser = factory(User::class)->states('user')->create();
        factory(Url::class, 2)->create(['user_id' => $secondUser->id]);

        $this->json('GET', '/web/urls')
            ->assertStatus(200)
            ->assertJsonCount(7, 'data');
    }

    public function test_user_can_get_own_url()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $url = factory(Url::class)->create(['user_id' => $user->id]);

        $this->json('GET', "/web/urls/{$url->path}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'path' => $url->path
                ]
            ]);
    }

    public function test_user_cant_get_other_user_url()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create(['user_id' => $secondUser->id]);

        $this->json('GET', "/web/urls/{$url->path}")
            ->assertStatus(401);
    }

    public function test_editor_can_get_other_user_url()
    {
        $user = factory(User::class)->states('editor')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create(['user_id' => $secondUser->id]);

        $this->json('GET', "/web/urls/{$url->path}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'path' => $url->path
                ]
            ]);
    }

    public function test_admin_can_get_other_user_url()
    {
        $user = factory(User::class)->states('administrator')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create(['user_id' => $secondUser->id]);

        $this->json('GET', "/web/urls/{$url->path}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'path' => $url->path
                ]
            ]);
    }

    public function test_guest_can_create_url()
    {        
        $data = [
            'long_url' => 'https://google.com'
        ];

        $this->json('POST', "/web/urls", $data)
            ->assertStatus(201)
            ->assertJson([
                'data' => $data
            ]);
    }
    
    public function test_user_can_create_url_without_path()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $data = [
            'long_url' => 'https://google.com'
        ];

        $this->json('POST', "/web/urls", $data)
            ->assertStatus(201)
            ->assertJson([
                'data' => $data
            ]);
    }

    public function test_user_can_create_url_with_path()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $data = [
            'path' => '1111111',
            'long_url' => 'https://google.com'
        ];

        $this->json('POST', "/web/urls", $data)
            ->assertStatus(201)
            ->assertJson([
                'data' => $data
            ]);
    }

    public function test_user_can_update_own_url()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $url = factory(Url::class)->create(['user_id' => $user->id]);

        $newData = [
            'active' => 0,
            'path' => '111123456',
            'long_url' => 'https://google.com'
        ];

        $this->json('POST', "/web/urls/{$url->id}", $newData)
            ->assertStatus(200)
            ->assertJson([
                'data' => $newData
            ]);
    }

    public function test_user_cant_update_other_user_url()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create(['user_id' => $secondUser->id]);

        $newData = [
            'active' => 0,
            'path' => '11111111',
            'long_url' => 'https://google.com'
        ];

        $this->json('POST', "/web/urls/{$url->id}", $newData)
            ->assertStatus(401);
    }

    public function test_editor_can_update_other_user_url()
    {
        $user = factory(User::class)->states('editor')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create(['user_id' => $secondUser->id]);

        $newData = [
            'active' => 0,
            'path' => '11111111',
            'long_url' => 'https://google.com'
        ];

        $this->json('POST', "/web/urls/{$url->id}", $newData)
            ->assertStatus(200)
            ->assertJson([
                'data' => $newData
            ]);
    }

    public function test_admin_can_update_other_user_url()
    {
        $user = factory(User::class)->states('administrator')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create(['user_id' => $secondUser->id]);

        $newData = [
            'active' => 0,
            'path' => '111111111',
            'long_url' => 'https://google.com'
        ];

        $this->json('POST', "/web/urls/{$url->id}", $newData)
            ->assertStatus(200)
            ->assertJson([
                'data' => $newData
            ]);
    }

    public function test_user_can_delete_own_url()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $url = factory(Url::class)->create(['user_id' => $user->id]);

        $this->json('delete', "/web/urls/{$url->id}")
            ->assertStatus(200);
    }

    public function test_user_cant_delete_other_user_url()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();
        $url = factory(Url::class)->create(['user_id' => $secondUser->id]);

        $this->json('delete', "/web/urls/{$url->id}")
            ->assertStatus(401);
    }

    public function test_editor_can_delete_other_user_url()
    {
        $user = factory(User::class)->states('editor')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();
        $url = factory(Url::class)->create(['user_id' => $secondUser->id]);

        $this->json('delete', "/web/urls/{$url->id}")
            ->assertStatus(200);
    }

    public function test_admin_can_delete_other_user_url()
    {
        $user = factory(User::class)->states('administrator')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();
        $url = factory(Url::class)->create(['user_id' => $secondUser->id]);

        $this->json('delete', "/web/urls/{$url->id}")
            ->assertStatus(200);
    }
}
