<?php

namespace Devpri\Tinre\Tests\Http\Controllers\Web;

use Carbon\Carbon;
use Devpri\Tinre\Models\Url;
use Devpri\Tinre\Models\User;
use Devpri\Tinre\Tests\TestCase;

class StatsControllerTest extends TestCase
{

    public function test_user_can_get_clicks()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $url = factory(Url::class)->create([
            'user_id' => $user->id
        ]);

        $this->get($url->path)
            ->assertStatus(302)
            ->assertRedirect($url->long_url);

        $this->json('GET', "/web/stats/{$url->id}/clicks", [
            'date' => [Carbon::now()->subDay(), Carbon::now()->addDay()]
        ])->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_user_cant_get_other_user_url_clicks()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create([
            'user_id' => $secondUser->id
        ]);

        $this->get($url->path)
            ->assertStatus(302)
            ->assertRedirect($url->long_url);

        $this->json('GET', "/web/stats/{$url->id}/clicks", [
            'date' => [Carbon::now()->subDay(), Carbon::now()->addDay()]
        ])->assertStatus(401);
    }

    public function test_editor_can_get_other_user_url_clicks()
    {
        $user = factory(User::class)->states('editor')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create([
            'user_id' => $secondUser->id
        ]);

        $this->get($url->path)
            ->assertStatus(302)
            ->assertRedirect($url->long_url);

        $this->json('GET', "/web/stats/{$url->id}/clicks", [
            'date' => [Carbon::now()->subDay(), Carbon::now()->addDay()]
        ])->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_admin_can_get_other_user_url_clicks()
    {
        $user = factory(User::class)->states('administrator')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create([
            'user_id' => $secondUser->id
        ]);

        $this->get($url->path)
            ->assertStatus(302)
            ->assertRedirect($url->long_url);

        $this->json('GET', "/web/stats/{$url->id}/clicks", [
            'date' => [Carbon::now()->subDay(), Carbon::now()->addDay()]
        ])->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_user_can_get_countries()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $url = factory(Url::class)->create([
            'user_id' => $user->id
        ]);

        $this->get($url->path)
            ->assertStatus(302)
            ->assertRedirect($url->long_url);

        $this->json('GET', "/web/stats/{$url->id}/country", [
            'date' => [Carbon::now()->subDay(), Carbon::now()->addDay()]
        ])->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_user_cant_get_other_user_url_countries()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create([
            'user_id' => $secondUser->id
        ]);

        $this->get($url->path)
            ->assertStatus(302)
            ->assertRedirect($url->long_url);

        $this->json('GET', "/web/stats/{$url->id}/country", [
            'date' => [Carbon::now()->subDay(), Carbon::now()->addDay()]
        ])->assertStatus(401);
    }

    public function test_editor_can_get_other_user_url_countries()
    {
        $user = factory(User::class)->states('editor')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create([
            'user_id' => $secondUser->id
        ]);

        $this->get($url->path)
            ->assertStatus(302)
            ->assertRedirect($url->long_url);

        $this->json('GET', "/web/stats/{$url->id}/country", [
            'date' => [Carbon::now()->subDay(), Carbon::now()->addDay()]
        ])->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_admin_can_get_other_user_url_countries()
    {
        $user = factory(User::class)->states('administrator')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create([
            'user_id' => $secondUser->id
        ]);

        $this->get($url->path)
            ->assertStatus(302)
            ->assertRedirect($url->long_url);

        $this->json('GET', "/web/stats/{$url->id}/country", [
            'date' => [Carbon::now()->subDay(), Carbon::now()->addDay()]
        ])->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_user_cant_get_unsupported_column()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $url = factory(Url::class)->create([
            'user_id' => $user->id
        ]);

        $this->json('GET', "/web/stats/{$url->id}/test", [
            'date' => [Carbon::now()->subDay(), Carbon::now()->addDay()]
        ])->assertStatus(422);
    }
}
