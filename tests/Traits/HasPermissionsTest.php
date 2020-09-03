<?php

namespace Devpri\Tinre\Tests\Traits;

use Devpri\Tinre\Models\User;
use Devpri\Tinre\Tests\TestCase;

class HasPermissionsTest extends TestCase
{
    public function test_can_get_user_permissions()
    {
        $user = factory(User::class)->states('user')->create();

        $permissions = $user->permissions();

        $this->assertTrue(array_diff($permissions, config('tinre.role_permissions.user')) === []);
    }

    public function test_can_get_admin_permissions()
    {
        $user = factory(User::class)->states('administrator')->create();

        $permissions = $user->permissions();

        $this->assertTrue(array_diff($permissions, config('tinre.permissions')) === []);
    }

    public function test_can_get_user_api_permissions()
    {
        $user = factory(User::class)->states('user')->create();

        $permissions = $user->apiPermissions();

        $this->assertTrue(array_diff($permissions, config('tinre.api_permissions')) === []);
    }

    public function test_can_get_editor_api_permissions()
    {
        $user = factory(User::class)->states('editor')->create();

        $permissions = $user->apiPermissions();

        $this->assertTrue(array_diff($permissions, config('tinre.api_permissions')) === []);
    }

    public function test_can_get_user_access_token_permissions()
    {
        $user = factory(User::class)->states('user')->create();
        $accessToken = $user->createToken('test', ['url:view', 'url:view:any']);

        $userWithAccessToken = $user->withAccessToken($accessToken);

        $permissions = $userWithAccessToken->permissions();

        $this->assertTrue(array_diff(['url:view'], $permissions) === []);
    }

    public function test_can_get_admin_access_token_permissions()
    {
        $user = factory(User::class)->states('administrator')->create();
        $accessToken = $user->createToken('test', ['url:view', 'url:view:any', 'test']);

        $userWithAccessToken = $user->withAccessToken($accessToken);

        $permissions = $userWithAccessToken->permissions();

        $this->assertTrue(array_diff(['url:view', 'url:view:any'], $permissions) === []);
    }
}
