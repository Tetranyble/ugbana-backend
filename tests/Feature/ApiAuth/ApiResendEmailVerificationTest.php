<?php

namespace Tests\Feature\ApiAuth;

use App\Models\User;
use App\Notifications\ApiEmailVerificationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ApiResendEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_can_resend_email_verification_notice_sent(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        Notification::assertNothingSent();
        $response = $this->postJson(route('v1.verification.send'), [
            'email' => $user->email,
        ])->assertStatus(200);

        Notification::assertSentTo(
            [$user], ApiEmailVerificationNotification::class
        );
    }
}
