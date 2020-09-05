<?php

namespace Devpri\Tinre\Tests\Http\Controllers\Web;

use Devpri\Tinre\Models\User;
use Devpri\Tinre\Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserControllerTest extends TestCase
{
    public function test_guest_cant_get_users()
    {
        $this->json('GET', '/web/users')
            ->assertStatus(401);
    }

    public function test_user_cant_get_users()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $this->json('GET', '/web/users')
            ->assertStatus(401);
    }

    public function test_editor_cant_get_users()
    {
        $user = factory(User::class)->states('editor')->create();
        $this->actingAs($user);

        $this->json('GET', '/web/users')
            ->assertStatus(401);
    }

    public function test_admin_can_get_users()
    {
        $user = factory(User::class)->states('administrator')->create();
        $this->actingAs($user);

        $this->json('GET', '/web/users')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'email' => $user->email,
            ]);
    }

    public function test_admin_can_search_users()
    {
        $user = factory(User::class)->states('administrator')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $this->json('GET', '/web/users', [
            'search' => $secondUser->email,
        ])
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'email' => $secondUser->email,
            ]);
    }

    public function test_cant_get_user()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $this->json('GET', "/web/users/{$secondUser->id}")
            ->assertStatus(401);
    }

    public function test_user_can_get_own_user()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $this->json('GET', "/web/users/{$user->id}")
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
        $this->actingAs($user);

        $this->json('GET', "/web/users/{$user->id}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => $user->email,
                ],
            ]);
    }

    public function test_user_cant_create_user()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $this->json('POST', '/web/users', [
            'active' => 1,
            'name' => 'test',
            'email' => 'test#test.com',
            'role' => 'user',
            'password' => 'testpassword',
        ])
            ->assertStatus(401);
    }

    public function test_admin_can_create_user()
    {
        $user = factory(User::class)->states('administrator')->create();
        $this->actingAs($user);

        $this->json('POST', '/web/users', [
            'active' => 1,
            'name' => 'test',
            'email' => 'test@test.com',
            'role' => 'user',
            'password' => 'testpassword',
        ])
            ->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'test',
                'email' => 'test@test.com',
                'role' => 'user',
            ]);
    }

    public function test_user_cant_update_user()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $this->json('POST', "/web/users/{$secondUser->id}", [
            'email' => 'test#test.com',
        ])
            ->assertStatus(401);
    }

    public function test_editor_cant_update_user()
    {
        $user = factory(User::class)->states('editor')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $this->json('POST', "/web/users/{$secondUser->id}", [
            'email' => 'test#test.com',
        ])
            ->assertStatus(401);
    }

    public function test_admin_cant_update_own_user()
    {
        $user = factory(User::class)->states('administrator')->create();
        $this->actingAs($user);

        $this->json('POST', "/web/users/{$user->id}", [
            'email' => 'test#test.com',
        ])
            ->assertStatus(401);
    }

    public function test_admin_can_update_user()
    {
        $user = factory(User::class)->states('administrator')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $this->json('POST', "/web/users/{$secondUser->id}", [
            'active' => $secondUser->active,
            'name' => $secondUser->name,
            'role' => $secondUser->role,
            'email' => 'test@test.com',
            'password' => 'newpassword',
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => 'test@test.com',
                ],
            ]);

        $updatedUser = User::where('id', $secondUser->id)->firstOrFail();

        $this->assertTrue(Hash::check('newpassword', $updatedUser->password));
    }

    public function test_user_cant_delete_user()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $this->json('DELETE', "/web/users/{$secondUser->id}")->assertStatus(401);
    }

    public function test_editor_cant_delete_user()
    {
        $user = factory(User::class)->states('editor')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $this->json('DELETE', "/web/users/{$secondUser->id}")->assertStatus(401);
    }

    public function test_admin_can_delete_user()
    {
        $user = factory(User::class)->states('administrator')->create();
        $this->actingAs($user);

        $secondUser = factory(User::class)->states('user')->create();

        $this->json('DELETE', "/web/users/{$secondUser->id}")->assertStatus(200);

        $updatedUser = User::where('id', $secondUser->id)->first();

        $this->assertTrue($updatedUser === null);
    }
}
