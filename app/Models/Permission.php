<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'label'];

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function assignRoles(Collection|Role $role)
    {

        return ($role instanceof Role) ?
            $this->roles()
                ->sync($role) :
            $this->roles()->sync(
                $role->pluck('id')
            );
    }
}
