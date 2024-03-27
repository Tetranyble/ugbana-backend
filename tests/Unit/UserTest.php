<?php

namespace Tests\Unit;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_has_a_firstname(): void
    {
        $firstname = $this->faker->firstName;
        $user = User::factory()->create([
            'firstname' => $firstname,
        ]);

        $this->assertEquals($firstname, $user->firstname);
    }

    /** @test */
    public function it_has_a_middlename(): void
    {
        $middlename = $this->faker->firstName;
        $user = User::factory()->create([
            'middlename' => $middlename,
        ]);

        $this->assertEquals($middlename, $user->middlename);
    }

    /** @test */
    public function it_has_a_lastname(): void
    {
        $lastname = $this->faker->firstName;
        $user = User::factory()->create([
            'lastname' => $lastname,
        ]);

        $this->assertEquals($lastname, $user->lastname);
    }

    /** @test */
    public function it_has_a_phone(): void
    {
        $phone = $this->faker->date;
        $user = User::factory()->create([
            'phone' => $phone,
        ]);

        $this->assertEquals($phone, $user->phone);
    }

    /** @test */
    public function it_has_a_status(): void
    {
        $status = UserStatus::ACTIVE;
        $user = User::factory()->create([
            'status' => $status,
        ]);

        $this->assertEquals($status, $user->status);
        $this->assertInstanceOf(UserStatus::class, $user->status);
    }

    /** @test */
    public function it_has_a_phone_verified_at(): void
    {
        $phone_verified_at = now();
        $user = User::factory()->create([
            'phone_verified_at' => $phone_verified_at,
        ]);

        $this->assertEquals($phone_verified_at->timestamp, $user->phone_verified_at->timestamp);
    }

    /** @test */
    public function it_has_a_email_verified_at(): void
    {
        $email_verified_at = now();
        $user = User::factory()->create([
            'email_verified_at' => $email_verified_at,
        ]);

        $this->assertEquals($email_verified_at->timestamp, $user->email_verified_at->timestamp);
    }

    /** @test */
    public function it_has_a_verification_code(): void
    {
        $verification_code = $this->faker->numberBetween(2000, 5000);
        $user = User::factory()->create([
            'verification_code' => $verification_code,
        ]);

        $this->assertEquals($verification_code, $user->verification_code);
    }

    /** @test */
    public function it_has_a_username(): void
    {
        $username = $this->faker->userName;

        $user = User::factory()->create([
            'username' => $username,
        ]);

        $this->assertEquals($username, $user->username);
    }

    /** @test */
    public function it_has_a_country(): void
    {
        $country = $this->faker->country();
        $user = User::factory()->create([
            'country' => $country,
        ]);

        $this->assertEquals($country, $user->country);
    }

    /** @test */
    public function it_has_a_is_accept_condition(): void
    {
        $is_accept_condition = $this->faker->boolean;
        $user = User::factory()->create([
            'is_accept_condition' => $is_accept_condition,
        ]);

        $this->assertEquals($is_accept_condition, $user->is_accept_condition);
    }
}
