<?php

namespace Devpri\Tinre\Tests\Http\Controllers;

use Devpri\Tinre\Models\User;
use Devpri\Tinre\Tests\TestCase;

class HomeControllerTest extends TestCase
{
    public function test_can_access_home_page()
    {
        $this->get('/')
            ->assertStatus(200);
    }

    public function test_redirect_user_to_dashboard()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $this->get('/')
            ->assertStatus(302)
            ->assertRedirect('dashboard');
    }
}
