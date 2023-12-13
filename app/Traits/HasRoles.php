<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Collection;

trait HasRoles
{
    /**
     * A user may have multiple roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * Determine if the user has the given role.
     *
     * @param  mixed  $role
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        return (bool) $role->intersect($this->roles)->count();
    }

    public function hasRoles($roles)
    {

        if (is_array($roles)) {
            return (bool) $this->roles()->whereIn('name', $roles)
                ->orWhereIn('label', $roles)->count();
        }
        if (is_string($roles)) {
            return (bool) $this->roles()->where('name', $roles)
                ->orWhere('label', $roles)->count();
        }

        return ($roles instanceof Collection) ?
            (bool) $this->roles->intersect($roles)->count() :
            (bool) $this->roles->intersect(collect([$roles]))->count();

    }

    public function assignRoles(...$roles)
    {

        return array_map(function ($role) {

            if (is_string($role)) {
                return $this->roles()->sync(
                    Role::where('name', $role)
                        ->orWhere('label', $role)->first(),
                    false
                );
            }

            return ($role instanceof Collection) ?

                $role->map(fn ($r) => $this->roles()->sync($r, false)) :

                $this->roles()->sync($role, false);

        }, $roles);
    }

    /**
     * Determine if the user may perform the given permission.
     *
     * @param  mixed|string  $permission
     * @return bool
     */
    public function hasPermissions(...$permissions)
    {
        $userPermissions = $this->permissions();

        return in_array(true, array_map(function ($permission) use ($userPermissions) {
            if (is_string($permission)) {
                return $userPermissions->contains($permission);
            }

            return ($permission instanceof Collection) ?
                $permission->map(fn ($p) => $userPermissions->contains($p->name)) :
                $userPermissions->contains($permission->name);

        }, $permissions));
    }

    /**
     * Get all user's permissions
     *
     * @return Collection $permission
     */
    public function permissions()
    {
        return collect([
            ...$this->roles->map->permissions->flatten()->pluck('name')->unique(),
            ...$this->userPermissions->pluck('name'),

        ]);
    }

    public function userPermissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_users')->withTimestamps();
    }

    public function assignPermissions(...$permissions)
    {
        return array_map(function ($permission) {

            if (is_string($permission)) {
                return $this->userPermissions()->sync(
                    Permission::where('name', $permission)
                        ->orWhere('label', $permission)->first(),
                    false
                );
            }

            return ($permission instanceof Collection) ?

                $permission->map(fn ($p) => $this->userPermissions()->sync($p, false)) :

                $this->userPermissions()->sync($permission, false);

        }, $permissions);
    }

    public function actAs($role)
    {
    }
}
