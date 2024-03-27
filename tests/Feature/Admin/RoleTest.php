<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Supports\PermissionSupport;
use Tests\TestCase;

class RoleTest extends TestCase
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
        $this->permissions('Role');
    }

    /** @test */
    public function it_get_list_of_roles()
    {

        $this->user->assignPermissions('role_index');

        Role::factory(20)->create();

        $response = $this->actingAs($this->user, 'api')
            ->getJson(route('v1.admin.roles.index'))
            ->assertOk();

        $response->assertJson(fn (AssertableJson $json) => $json->has('status')
            ->has('message')
            ->has('meta')
            ->has('links')
            ->has('data', 10)

            ->etc()
        );
    }

    /** @test */
    public function it_store_role()
    {
        $this->user->assignPermissions('role_store');

        $response = $this->actingAs($this->user, 'api')
            ->postJson(route('v1.admin.roles.store'), [
                'name' => 'Imperial',
            ])
            ->assertStatus(201);

        $response->assertJson(fn (AssertableJson $json) => $json->has('status')
            ->has('message')
            ->where('data.label', 'Imperial')
            ->etc()
        );
    }

    /** @test */
    public function it_update_role()
    {
        $this->markTestSkipped();
        $this->user->assignPermissions('role_update');

        $role = Role::factory()->create([
            'is_system' => false,
        ]);
        $response = $this->actingAs($this->user, 'api')
            ->patchJson(route('v1.admin.roles.update',
                ['role' => $role->id]), [
                    'name' => 'Imperial',
                ])
            ->assertStatus(200);

        $response->assertJson(fn (AssertableJson $json) => $json->has('status')
            ->has('message')
            ->where('data.label', 'Imperial')
            ->etc()
        );
    }

    /** @test */
    public function it_shows_role()
    {
        $this->user->assignPermissions('role_show');

        $role = Role::factory()->create([
            'label' => 'Imperial',
        ]);
        $response = $this->actingAs($this->user, 'api')
            ->getJson(route('v1.admin.roles.show', ['role' => $role->id]))
            ->assertStatus(200);

        $response->assertJson(fn (AssertableJson $json) => $json->has('status')
            ->has('message')
            ->where('data.label', 'Imperial')
            ->etc()
        );
    }

    /** @test */
    public function it_delete_role()
    {
        $this->markTestSkipped();
        $this->user->assignPermissions('role_delete');

        $role = Role::factory()->create([
            'label' => 'Imperial',
            'is_system' => false,
        ]);
        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(route('v1.admin.roles.delete', ['role' => $role->id]))
            ->assertStatus(204);

    }

    /** @test */
    public function system_role_update_should_fail()
    {
        $this->markTestSkipped();
        $this->user->assignPermissions('role_update');

        $role = Role::factory()->create([
            'is_system' => true,
        ]);
        $response = $this->actingAs($this->user, 'api')
            ->patchJson(route('v1.admin.roles.update', ['role' => $role->id]), [
                'name' => 'Imperial',
            ])
            ->assertStatus(403);
    }

    /** @test */
    public function system_role_delete_should_fail()
    {
        $this->markTestSkipped();
        $this->user->assignPermissions('role_update');

        $role = Role::factory()->create([
            'is_system' => true,
        ]);
        $response = $this->actingAs($this->user, 'api')
            ->patchJson(route('v1.admin.roles.delete', ['role' => $role->id]), [
                'name' => 'Imperial',
            ])
            ->assertStatus(403);
    }
}
