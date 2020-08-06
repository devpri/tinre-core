<?php

namespace Devpri\Tinre\Tests\Console\Commands;

use Devpri\Tinre\Models\User;
use Devpri\Tinre\Tests\TestCase;

class CreateUserTest extends TestCase
{
    public function test_can_create_user()
    {
        $this->artisan('user:create')
            ->expectsQuestion('Name?', 'Test')
            ->expectsQuestion('Email?', 'test@test.com')
            ->expectsQuestion('Password?', 'testpassword')
            ->expectsQuestion('Role?', 'user')
            ->expectsOutput('User created!')
            ->assertExitCode(0);

        $user = User::where('email', 'test@test.com')->firstOrFail();

        $this->assertSame($user->name, 'Test');
    }
}
