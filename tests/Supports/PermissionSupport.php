<?php

namespace Tests\Supports;

use App\Models\Permission;
use Illuminate\Support\Str;

trait PermissionSupport
{
    public function permissions($class)
    {
        $permissions = collect(["\App\Models\\".$class])->map(function ($model) {
            $m = explode('\\', $model)[3];

            return [$m.' Create', $m.' View', $m.' Update', $m.' Delete',
                $m.' Store', $m.' Show', $m.' Index', $m.' Edit'];
        })->flatten()->map(function ($permission) {
            return Permission::factory()->create([
                'name' => Str::slug($permission, '_'),
                'label' => $permission,
            ]);
        });

        return $permissions;
    }
}
