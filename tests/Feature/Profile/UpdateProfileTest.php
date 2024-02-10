<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_may_update_profile(): void
    {

        $user = User::factory()->create();
        $data = $this->userData();

        $this->assertNotEquals($data['firstname'], $user->firstname);
        $this->assertNotEquals($data['middlename'], $user->middlename);
        $this->assertNotEquals($data['lastname'], $user->lastname);
        $response = $this->actingAs($user, 'api')
            ->patchJson(route('v1.profile.update'), $data)
            ->assertOk();
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('status')
            ->where('message', 'success')
            ->where('data.id', $user->id)
            ->where('data.firstname', $data['firstname'])
            ->where('data.lastname', $data['lastname'])
            ->etc()
        );

    }

    /** @test */
    public function it_should_ignore_empty_or_null_field_provide_for_update(): void
    {

        $user = User::factory()->create();
        $data = $this->userData();
        $data['middlename'] = '';
        $response = $this->actingAs($user, 'api')
            ->patchJson(route('v1.profile.update'), $data)
            ->assertOk();
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('status')
            ->where('message', 'success')
            ->where('data.id', $user->id)
            ->where('data.firstname', $data['firstname'])
            ->where('data.lastname', $data['lastname'])

            ->where('data.middlename', $user->middlename)
            ->etc()
        );

    }

    private function userData()
    {
        return User::factory()->raw();
    }
}
