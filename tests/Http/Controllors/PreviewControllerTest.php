<?php

namespace Devpri\Tinre\Tests\Http\Controllers;

use Devpri\Tinre\Models\Url;
use Devpri\Tinre\Tests\TestCase;

class PreviewControllerTest extends TestCase
{
    public function test_is_redirected_to_home()
    {
        $this->get('wrong-url+')
            ->assertRedirect('/');
    }

    public function test_can_access_url_preview()
    {
        $url = factory(Url::class)->create();

        $this->get($url->path . config('tinre.url_preview_suffix', '+'))
            ->assertStatus(200)
            ->assertSee($url->path)
            ->assertSee($url->long_url);
    }
}
