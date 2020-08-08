<?php

namespace Devpri\Tinre\Tests\Http\Controllers\Auth;

use Devpri\Tinre\Models\User;
use Devpri\Tinre\Notifications\ChangeEmailNotification;
use Devpri\Tinre\Tests\TestCase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;

class ChangeEmailControllerTest extends TestCase
{
    public function test_can_change_email()
    {
        Notification::fake();

        $user = factory(User::class)->states('user')->create();
        $this->actingAs($user);

        $this->json('POST', 'dashboard/email/change', ['email' => 'new@test.com', 'password' => 'password'])
            ->assertStatus(200);

        Notification::assertSentTo(
            new AnonymousNotifiable,
            ChangeEmailNotification::class,
            function ($notification, $channels, $notifiable) use ($user) {
                $content = $notification->toMail($user)->toArray();

                $this->get($content['actionUrl'])
                    ->assertStatus(302)
                    ->assertRedirect('dashboard');

                $newUser = User::where('id', $user->id)->first();

                return $notifiable->routes['mail'] === $newUser->email;
            }
        );
    }
}
