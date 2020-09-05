<?php

namespace Devpri\Tinre\Tests\Http\Controllers;

use Devpri\Tinre\Models\User;
use Devpri\Tinre\Tests\TestCase;

class HomeControllerTest extends TestCase
{
    protected function disable_redirect_user_to_dashboard($app)
    {
        $app->config->set('tinre.redirect_user_to_dashboard', false);
    }

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

    /**
     * @environment-setup disable_redirect_user_to_dashboard
     */
    public function test_user_can_access_hmme_page()
    {
        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $this->get('/')
            ->assertStatus(200);
    }
}
