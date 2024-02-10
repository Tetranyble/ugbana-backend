<?php

namespace Tests\Supports;

use App\Models\Permission;
use App\Models\Role;

trait RolePermissionSupport
{
    public function roleAndPermissions(int $roleCount = 2, int $permissionCount = 2)
    {

        return tap(Permission::factory($permissionCount)->create())
            ->map(function ($permission) use ($roleCount) {
                $permission->assignRoles(
                    Role::factory($roleCount)->create()
                );
            });
    }

    public function makePrimaryRoles()
    {
        return tap(Permission::factory(1)->create())
            ->map(function ($permission) {
                $permission->assignRoles(
                    collect([
                        Role::factory()->create([
                            'name' => 'parent',
                            'label' => 'parent',
                        ]),
                        Role::factory()->create([
                            'name' => 'student',
                            'label' => 'student',
                        ]),
                        Role::factory()->create([
                            'name' => 'tutor',
                            'label' => 'tutor',
                        ]),
                    ])
                );
            });
    }
}
