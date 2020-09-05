<?php

namespace Devpri\Tinre\Tests\Http\Controllers;

use Devpri\Tinre\Models\Url;
use Devpri\Tinre\Tests\TestCase;

class RedirectControllerTest extends TestCase
{
    protected function change_redirect_type($app)
    {
        $app->config->set('tinre.redirect_type', 301);
    }

    public function test_is_redirected_to_home()
    {
        $this->get('wrong-url')
            ->assertRedirect('/');
    }

    public function test_is_redirected_to_site()
    {
        $url = factory(Url::class)->create();

        $this->get($url->path)
            ->assertStatus(302)
            ->assertRedirect($url->long_url);

        $updatedUrl = Url::where('id', $url->id)->first();

        $this->assertTrue((int) $updatedUrl->total_clicks === 1);
        $this->assertTrue($url->clicks->count() === 1);
    }

    public function test_dont_count_bots()
    {
        $url = factory(Url::class)->create();

        $this->get($url->path, [
            'HTTP_USER-AGENT' => 'Crawler',
        ])
            ->assertStatus(302)
            ->assertRedirect($url->long_url);

        $updatedUrl = Url::where('id', $url->id)->first();

        $this->assertTrue((int) $updatedUrl->total_clicks === 0);
        $this->assertTrue($url->clicks->count() === 0);
    }

    /**
     * @environment-setup change_redirect_type
     */
    public function test_is_redirected_to_site_301()
    {
        $url = factory(Url::class)->create();

        $this->get($url->path)
            ->assertStatus(301)
            ->assertRedirect($url->long_url);
    }
}
