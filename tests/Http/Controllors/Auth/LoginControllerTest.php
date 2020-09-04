<?php

namespace Devpri\Tinre\Tests\Http\Controllers\Auth;

use Devpri\Tinre\Models\User;
use Devpri\Tinre\Tests\TestCase;

class LoginControllerTest extends TestCase
{
    public function test_can_access_login_page()
    {
        $this->get('/dashboard/login')
            ->assertStatus(200);
    }

    public function test_redirect_user_to_dashboard()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $this->get('/dashboard/login')
            ->assertStatus(302)
            ->assertRedirect('dashboard');
    }

    public function test_can_login()
    {
        $user = factory(User::class)->states('user')->create();

        $this->post('dashboard/login', ['email' => $user->email, 'password' => 'password'])
            ->assertStatus(302)
            ->assertRedirect('dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_cant_login_with_wrong_credentials()
    {
        $user = factory(User::class)->states('user')->create();

        $this->post('dashboard/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertStatus(302)
            ->assertRedirect();

        $this->assertGuest();
    }

    public function test_cant_login_with_disabled_user()
    {
        $user = factory(User::class)->states(['user', 'disabled'])->create();

        $this->post('dashboard/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertStatus(302)
            ->assertRedirect();

        $this->assertGuest();
    }
}
