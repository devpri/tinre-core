<?php

namespace Devpri\Tinre\Tests\Http\Controllers;

use Devpri\Tinre\Models\Url;
use Devpri\Tinre\Tests\TestCase;

class RedirectControllerTest extends TestCase
{
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
}
