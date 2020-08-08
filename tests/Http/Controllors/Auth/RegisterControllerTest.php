<?php

namespace Devpri\Tinre\Tests\Http\Controllers\Auth;

use Devpri\Tinre\Models\User;
use Devpri\Tinre\Notifications\EmailVerificationNotification;
use Devpri\Tinre\Tests\TestCase;
use Illuminate\Support\Facades\Notification;

class RegisterControllerTest extends TestCase
{
    public function test_can_access_register_page()
    {
        $this->get('/dashboard/register')
            ->assertStatus(200);
    }

    public function test_can_create_an_account()
    {
        Notification::fake();

        $data = [
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => '12345678',
        ];

        $this->post('dashboard/register', $data)
            ->assertStatus(302)
            ->assertRedirect('dashboard');

        $user = User::where('email', $data['email'])->firstOrFail();

        Notification::assertSentTo($user, function (EmailVerificationNotification $notification, $channels) use ($user) {
            $content = $notification->toMail($user)->toArray();

            $this->get($content['actionUrl'])
                ->assertStatus(302)
                ->assertRedirect('dashboard');

            $newUser = User::where('email', $user->email)->first();

            return $newUser->email_verified_at != null;
        });
    }
}
