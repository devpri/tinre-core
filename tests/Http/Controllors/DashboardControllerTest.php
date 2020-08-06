<?php

namespace Devpri\Tinre\Tests\Http\Controllers;

use Devpri\Tinre\Models\User;
use Devpri\Tinre\Tests\TestCase;

class DashboardControllerTest extends TestCase
{

    public function test_is_redirected_to_login_page()
    {
        $this->get('dashboard')
            ->assertStatus(302)
            ->assertRedirect('dashboard/login');
    }

    public function test_can_access_login_page()
    {
        $user = factory(User::class)->states('user')->create();

        $this->actingAs($user);

        $this->get('dashboard')
            ->assertStatus(200);
    }
}
