<?php

namespace Devpri\Tinre\Tests\Http\Controllers\Web;

use Carbon\Carbon;
use Devpri\Tinre\Models\Click;
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
            'user_id' => $user->id,
        ]);

        $now = Carbon::now();

        factory(Click::class, 5)->create([
            'url_id' => $url->id,
            'created_at' => $now,
        ]);

        factory(Click::class, 5)->create([
            'url_id' => $url->id,
            'created_at' => $now->copy()->subDays(10),
        ]);

        $this->json('GET', "/web/stats/{$url->id}/clicks", [
            'start_date' => $now->copy()->subDay(),
            'end_date' => $now->copy()->addDay(),
        ])->assertStatus(200)
            ->assertJsonFragment(['value' => '5']);
    }

    public function test_user_cant_get_other_user_url_clicks()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create([
            'user_id' => $secondUser->id,
        ]);

        $this->json('GET', "/web/stats/{$url->id}/clicks", [
            'start_date' => Carbon::now()->subDay(),
            'end_date' => Carbon::now()->addDay(),
        ])->assertStatus(401);
    }

    public function test_editor_can_get_other_user_url_clicks()
    {
        $user = factory(User::class)->states('editor')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create([
            'user_id' => $secondUser->id,
        ]);

        $now = Carbon::now();

        factory(Click::class, 5)->create([
            'url_id' => $url->id,
            'created_at' => $now,
        ]);

        factory(Click::class, 5)->create([
            'url_id' => $url->id,
            'created_at' => $now->copy()->subDays(10),
        ]);

        $this->json('GET', "/web/stats/{$url->id}/clicks", [
            'start_date' => $now->copy()->subDay(),
            'end_date' => $now->copy()->addDay(),
        ])->assertStatus(200)
            ->assertJsonFragment(['value' => '5']);
    }

    public function test_admin_can_get_other_user_url_clicks()
    {
        $user = factory(User::class)->states('administrator')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create([
            'user_id' => $secondUser->id,
        ]);

        $now = Carbon::now();

        factory(Click::class, 5)->create([
            'url_id' => $url->id,
            'created_at' => $now,
        ]);

        factory(Click::class, 5)->create([
            'url_id' => $url->id,
            'created_at' => $now->copy()->subDays(10),
        ]);

        $this->json('GET', "/web/stats/{$url->id}/clicks", [
            'start_date' => $now->copy()->subDay(),
            'end_date' => $now->copy()->addDay(),
        ])->assertStatus(200)
            ->assertJsonFragment(['value' => '5']);
    }

    public function test_user_can_get_countries()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $url = factory(Url::class)->create([
            'user_id' => $user->id,
        ]);

        $now = Carbon::now();

        $clicks = factory(Click::class, 5)->create([
            'url_id' => $url->id,
            'created_at' => $now,
        ]);

        factory(Click::class, 5)->create([
            'url_id' => $url->id,
            'created_at' => $now->copy()->subDays(10),
        ]);

        $this->json('GET', "/web/stats/{$url->id}/country", [
            'start_date' => $now->copy()->subDay(),
            'end_date' => $now->copy()->addDay(),
        ])->assertStatus(200)
            ->assertJsonFragment(['label' => $clicks->first()->country]);
    }

    public function test_user_cant_get_other_user_url_countries()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create([
            'user_id' => $secondUser->id,
        ]);

        $this->json('GET', "/web/stats/{$url->id}/country", [
            'start_date' => Carbon::now()->subDay(),
            'end_date' => Carbon::now()->addDay(),
        ])->assertStatus(401);
    }

    public function test_editor_can_get_other_user_url_countries()
    {
        $user = factory(User::class)->states('editor')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create([
            'user_id' => $secondUser->id,
        ]);

        $now = Carbon::now();

        $clicks = factory(Click::class, 5)->create([
            'url_id' => $url->id,
            'created_at' => $now,
        ]);

        factory(Click::class, 5)->create([
            'url_id' => $url->id,
            'created_at' => $now->copy()->subDays(10),
        ]);

        $this->json('GET', "/web/stats/{$url->id}/country", [
            'start_date' => $now->copy()->subDay(),
            'end_date' => $now->copy()->addDay(),
        ])->assertStatus(200)
            ->assertJsonFragment(['label' => $clicks->first()->country]);
    }

    public function test_admin_can_get_other_user_url_countries()
    {
        $user = factory(User::class)->states('administrator')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create([
            'user_id' => $secondUser->id,
        ]);

        $now = Carbon::now();

        $clicks = factory(Click::class, 5)->create([
            'url_id' => $url->id,
            'created_at' => $now,
        ]);

        factory(Click::class, 5)->create([
            'url_id' => $url->id,
            'created_at' => $now->copy()->subDays(10),
        ]);

        $this->json('GET', "/web/stats/{$url->id}/country", [
            'start_date' => $now->copy()->subDay(),
            'end_date' => $now->copy()->addDay(),
        ])->assertStatus(200)
            ->assertJsonFragment(['label' => $clicks->first()->country]);
    }
}
