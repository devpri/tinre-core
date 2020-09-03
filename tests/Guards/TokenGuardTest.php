<?php

namespace Devpri\Tinre\Tests\Guards;

use Devpri\Tinre\Guards\TokenGuard;
use Devpri\Tinre\Models\User;
use Devpri\Tinre\Tests\TestCase;
use Illuminate\Http\Request;

class TokenGuardTest extends TestCase
{
    public function test_can_get_user()
    {
        $user = factory(User::class)->states('user')->create();

        $accessToken = $user->createToken('test', ['url:view']);

        $request = Request::create('/');
        $request->headers->set('Authorization', "Bearer {$accessToken->plain_text_token}");

        $guard = new TokenGuard($request);

        $guardUser = $guard->user();

        $this->assertTrue($user->id === $guardUser->id);
        $this->assertTrue($accessToken->id === $guardUser->accessToken->id);
    }

    public function test_cant_get_disabled_user()
    {
        $user = factory(User::class)->states('user')->create([
            'active' => 0,
        ]);

        $accessToken = $user->createToken('test', ['url:view']);

        $request = Request::create('/');
        $request->headers->set('Authorization', "Bearer {$accessToken->plain_text_token}");

        $guard = new TokenGuard($request);

        $guardUser = $guard->user();

        $this->assertTrue($guardUser === null);
    }

    public function test_cant_get_user_without_bearer_token()
    {
        $request = Request::create('/');

        $guard = new TokenGuard($request);

        $guardUser = $guard->user();

        $this->assertTrue($guardUser === null);
    }

    public function test_cant_get_user_with_wrong_bearer_token()
    {
        $user = factory(User::class)->states('user')->create();

        $request = Request::create('/');
        $request->headers->set('Authorization', 'Bearer wrong');

        $guard = new TokenGuard($request);

        $guardUser = $guard->user();

        $this->assertTrue($guardUser === null);
    }

    public function test_cant_get_user_without_api_role()
    {
        $user = factory(User::class)->create([
            'role' => 'test',
        ]);

        $accessToken = $user->createToken('test', ['url:view']);

        $request = Request::create('/');
        $request->headers->set('Authorization', "Bearer {$accessToken->plain_text_token}");

        $guard = new TokenGuard($request);

        $guardUser = $guard->user();

        $this->assertTrue($guardUser === null);
    }
}
