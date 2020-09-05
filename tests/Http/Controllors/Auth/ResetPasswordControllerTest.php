<?php

namespace Devpri\Tinre\Tests\Http\Controllers\Auth;

use Devpri\Tinre\Models\User;
use Devpri\Tinre\Notifications\ResetPasswordNotification;
use Devpri\Tinre\Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class ResetPasswordControllerTest extends TestCase
{
    public function test_can_access_reset_page()
    {
        $this->get('dashboard/password/reset')
            ->assertStatus(200);
    }

    public function test_can_reset_password()
    {
        Notification::fake();

        $user = factory(User::class)->states('user')->create();

        $this->followingRedirects()->post('dashboard/password/email', ['email' => $user->email])
            ->assertStatus(200);

        Notification::assertSentTo(
            $user,
            function (ResetPasswordNotification $notification, $channels) use ($user) {
                $this->post('dashboard/password/reset', ['token' => $notification->token, 'email' => $user->email, 'password' => 'testtest1', 'password_confirmation' => 'testtest1'])
                    ->assertStatus(302)
                    ->assertRedirect('dashboard/login');

                $newUser = User::where('email', $user->email)->first();

                return Hash::check('testtest1', $newUser->password);
            }
        );
    }

    public function test_cant_reset_password_with_wrong_email()
    {
        Notification::fake();

        factory(User::class)->states('user')->create();

        $this->post('dashboard/password/email', ['email' => 'wrong@email.test'])
            ->assertStatus(302);

        Notification::assertNothingSent();
    }
}
