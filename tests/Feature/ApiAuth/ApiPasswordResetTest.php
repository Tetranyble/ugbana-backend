<?php

namespace Tests\Feature\ApiAuth;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ApiPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function reset_password_code_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->postJson(route('v1.forgot.password'),
            ['email' => $user->email])->assertOk();

        Notification::assertSentTo($user, PasswordResetNotification::class);
    }

    /** @test */
    public function password_can_be_reset_with_valid_token(): void
    {
        $this->withoutExceptionHandling();
        Notification::fake();

        $user = User::factory()->create();

        $this->postJson(route('v1.forgot.password'), [
            'email' => $user->email,
        ])->assertOk();

        Notification::assertSentTo($user, PasswordResetNotification::class, function (object $notification) use ($user) {
            $response = $this->postJson(route('v1.reset.password'), [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ])->assertOk();

            $response->assertSessionHasNoErrors();

            return true;
        });
    }
}
