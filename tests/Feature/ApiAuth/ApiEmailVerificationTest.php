<?php

namespace Tests\Feature\ApiAuth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ApiEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function email_can_be_verified(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $user->phoneVerification();

        Event::fake();

        $response = $this->getJson(route('v1.verification.verify', [
            'user' => $user->email,
            'code' => $user->verification_code,
        ]))->assertStatus(200);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());

    }

    /** @test */
    public function email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $user->phoneVerification();

        Event::fake();

        $response = $this->getJson(route('v1.verification.verify', [
            'user' => $user->email,
            'code' => '049487',
        ]))->assertStatus(422);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());

    }
}
