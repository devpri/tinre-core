<?php

namespace Devpri\Tinre\Tests\Http\Controllers\Auth;

use Devpri\Tinre\Models\User;
use Devpri\Tinre\Notifications\EmailVerificationNotification;
use Devpri\Tinre\Tests\TestCase;
use Illuminate\Support\Facades\Notification;

class VerificationControllerTest extends TestCase
{
    public function test_cant_access_verification_page()
    {
        $this->get('dashboard/email/verification')
            ->assertStatus(302);
    }

    public function test_can_access_verification_page()
    {
        $user = factory(User::class)->states('user')->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user);

        $this->get('dashboard/email/verify')
            ->assertStatus(200);
    }

    public function test_redirect_verified_user()
    {
        $user = factory(User::class)->states('user')->create();

        $this->actingAs($user);

        $this->get('dashboard/email/verify')
            ->assertStatus(302);
    }

    public function test_can_verify_email()
    {
        Notification::fake();

        $user = factory(User::class)->states('user')->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user);

        $this->followingRedirects()->post('dashboard/email/resend')
            ->assertStatus(200);

        Notification::assertSentTo(
            $user,
            function (EmailVerificationNotification $notification, $channels) use ($user) {
                $this->get($notification->toMail($user)->actionUrl)
                    ->assertStatus(302)
                    ->assertRedirect('dashboard');

                $newUser = User::where('email', $user->email)->first();

                return $newUser->email_verified_at != null;
            }
        );
    }

    public function test_camt_verify_email()
    {
        $user = factory(User::class)->states('user')->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user);

        $this->get('/dashboard/email/verify/1/wrong')
            ->assertStatus(403);
    }
}
