<?php

namespace Tests\Unit;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_a_name()
    {

        $roleName = 'supervisor';

        $role = Role::factory()->create([
            'name' => $roleName,
        ]);

        $this->assertEquals($roleName, $role->name);
    }

    /** @test */
    public function it_has_a_label()
    {

        $label = 'supervisor';

        $role = Role::factory()->create([
            'label' => $label,
        ]);

        $this->assertEquals($label, $role->label);
    }

    /** @test */
    public function it_has_a_description()
    {

        $description = 'A supervisor is in D level';

        $role = Role::factory()->create([
            'description' => $description,
        ]);

        $this->assertEquals($description, $role->description);
    }

    /** @test */
    public function it_has_is_system()
    {

        $system = true;

        $role = Role::factory()->create([
            'is_system' => $system,
        ]);

        $this->assertEquals($system, $role->is_system);
    }
}
