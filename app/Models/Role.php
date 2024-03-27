<?php

namespace App\Models;

use App\Traits\HasRoles;
use Illuminate\Database\Eloquent\Builder;
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

    public function scopeSearch(Builder $builder, ?string $terms = null)
    {
        $builder->where(function ($builder) use ($terms) {
            collect(explode(' ', $terms))->filter()->each(function ($term) use ($builder) {
                $term = '%'.$term.'%';
                $builder->orWhere('name', 'like', $term)
                    ->orWhere('label', 'like', $term);
            });
        });
    }

    public function isSystem(): bool
    {
        return $this->is_system;
    }
}
