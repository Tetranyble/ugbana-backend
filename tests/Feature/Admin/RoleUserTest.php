<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Supports\PermissionSupport;
use Tests\TestCase;

class RoleUserTest extends TestCase
{
    use PermissionSupport, RefreshDatabase, WithFaker;

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
    public function attach_role_to_user()
    {

        $this->user->assignPermissions('user_store');
        $this->user->assignPermissions('user_update');

        $role = Role::factory()->create([
            'name' => 'imperial',
        ]);
        $this->assertCount(1, $this->user->roles);
        $response = $this->actingAs($this->user, 'api')
            ->postJson(route('v1.admin.users.roles.attach', [
                'user' => $this->user->id,
            ]), [
                'role' => $role->id,
            ])
            ->assertOk();

        $response->assertJson(fn (AssertableJson $json) => $json->has('status')
            ->has('message')
            ->has('data', 2)

            ->etc()
        );
    }

    /** @test */
    public function de_attach_role_to_user()
    {

        $this->user->assignPermissions('user_store');
        $this->user->assignPermissions('user_update');

        $role = Role::factory()->create([
            'name' => 'imperial',
        ]);
        $this->user->assignRoles($role->id);
        $this->assertCount(2, $this->user->roles);
        $response = $this->actingAs($this->user, 'api')
            ->patchJson(route('v1.admin.users.roles.deattach', [
                'user' => $this->user->id,
            ]), [
                'role' => $role->id,
            ])
            ->assertOk();

        $response->assertJson(fn (AssertableJson $json) => $json->has('status')
            ->has('message')
            ->has('data', 1)

            ->etc()
        );
    }
}
