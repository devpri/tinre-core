<?php

namespace Devpri\Tinre\Tests\Http\Controllers;

use Devpri\Tinre\Tests\TestCase;

class HomeControllerTest extends TestCase
{

    public function test_can_access_home_page()
    {
        $this->get('/')
            ->assertStatus(200);
    }
}
