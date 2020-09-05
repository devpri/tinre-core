<?php

namespace Devpri\Tinre\Tests\Http\Controllers;

use Devpri\Tinre\Models\Url;
use Devpri\Tinre\Tests\TestCase;

class PreviewControllerTest extends TestCase
{
    protected function disable_url_preview($app)
    {
        $app->config->set('tinre.url_preview', false);
    }

    public function test_is_redirected_to_home()
    {
        $this->get('wrong-url+')
            ->assertRedirect('/');
    }

    public function test_can_access_url_preview()
    {
        $url = factory(Url::class)->create();

        $this->get($url->path.config('tinre.url_preview_suffix', '+'))
            ->assertStatus(200)
            ->assertSee($url->path)
            ->assertSee($url->long_url);
    }

    /**
     * @environment-setup disable_url_preview
     */
    public function test_redirect_to_home_when_url_preview_is_disabled()
    {
        $url = factory(Url::class)->create();

        $this->get($url->path.config('tinre.url_preview_suffix', '+'))
            ->assertStatus(302)
            ->assertRedirect('/');
    }
}
