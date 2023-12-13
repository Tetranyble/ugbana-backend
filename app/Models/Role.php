<?php

namespace App\Models;

use App\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, HasRoles;

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'label', 'description'];

    /**
     * @var string[]
     */
    protected $casts = ['is_system' => 'boolean'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }

    public function givePermissionTo(Permission $permission)
    {
        return $this->permissions()->sync($permission, false);
    }
}
