<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Supports\PermissionSupport;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use PermissionSupport, RefreshDatabase , WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->role = Role::factory()->create([
            'label' => 'Manager',
            'name' => 'manager',
        ]);

        $this->user->assignRoles('manager');
        $this->permissions('User');
    }

    /** @test */
    public function an_admin_can_create_new_employee(): void
    {
        //$this->user->assignPermissions('user_store');

        $response = $this->actingAs($this->user, 'api')
            ->postJson(route('v1.admin.employee.store'),
                $user = User::factory()->raw([
                    'firstname' => 'Ugbanawaji',
                    'lastname' => 'Ekenekiso',
                    'password' => 'password',
                    'password_confirmation' => 'password',
                ])
            )->assertStatus(201);

        $response->assertJson(fn (AssertableJson $json) => $json
            ->where('status', true)
            ->where('message', 'Please verify your email address')
            ->where('data.firstname', 'Ugbanawaji')
            ->where('data.lastname', 'Ekenekiso')
            ->etc()
        );

    }

    /** @test */
    public function an_admin_can_update_employee_record(): void
    {
        //$this->user->assignPermissions('user_store');
        $user = User::factory()->create();
        $response = $this->actingAs($this->user, 'api')
            ->patchJson(route('v1.admin.employee.update', ['user' => $user->id]),
                [
                    'firstname' => 'Update name',
                    'lastname' => 'Ekenekiso',
                ]
            )->assertStatus(200);

        $response->assertJson(fn (AssertableJson $json) => $json
            ->where('status', true)
            ->where('message', 'success')
            ->where('data.firstname', 'Update name')
            ->where('data.lastname', 'Ekenekiso')
            ->etc()
        );

    }

    /** @test */
    public function an_admin_can_index_employee_record(): void
    {
        $user = User::factory(50)->create();
        $response = $this->actingAs($this->user, 'api')
            ->getJson(route('v1.admin.employee.index'))
            ->assertStatus(200);

        $response->assertJson(fn (AssertableJson $json) => $json->has('status')
            ->has('message')
            ->has('meta')
            ->has('links')
            ->has('data', 10)

            ->etc()
        );

    }

    /** @test */
    public function given_an_employee_an_admin_can_see_employee_record(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($this->user, 'api')
            ->getJson(route('v1.admin.employee.show', [
                'user' => $user->id,
            ]))
            ->assertStatus(200);

        $response->assertJson(fn (AssertableJson $json) => $json
            ->where('status', true)
            ->where('message', 'success')
            ->where('data.firstname', $user->firstname)
            ->where('data.lastname', $user->lastname)
            ->where('data.id', $user->id)
            ->etc()
        );

    }

    /** @test */
    public function given_an_employee_an_admin_can_delete_employee_record(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(route('v1.admin.employee.destroy', [
                'user' => $user->id,
            ]))
            ->assertStatus(204);

    }
}
