<?php

namespace Devpri\Tinre\Tests\Http\Controllers\Web;

use Devpri\Tinre\Models\User;
use Devpri\Tinre\Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class LoggedUserControllerTest extends TestCase
{
    public function test_cant_get_user()
    {
        $this->json('GET', '/web/users/me')
            ->assertStatus(401);
    }

    public function test_can_get_user()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $this->json('GET', '/web/users/me')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ]);
    }

    public function test_can_update_user()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $this->json('POST', '/web/users/me', [
            'name' => 'New Name',
            'current_password' => 'password',
            'new_password' => 'newpassword',
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'New Name',
                ],
            ]);

        $updatedUser = User::where('id', $user->id)->first();

        $this->assertTrue(Hash::check('newpassword', $updatedUser->password));
    }

    public function test_cant_update_password()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $this->json('POST', '/web/users/me', [
            'name' => 'New Name',
            'current_password' => 'wrongpassword',
            'new_password' => 'newpassword',
        ])->assertStatus(422);
    }
}
