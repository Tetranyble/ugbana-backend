<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\StorageProvider;
use App\Enums\UserStatus;
use App\Http\Resources\UserResource;
use App\Traits\ApiMustVerify;
use App\Traits\HasRoles;
use App\Traits\Thumbnail;
use App\Traits\WithAttribute;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use ApiMustVerify, HasApiTokens,HasFactory, HasRoles,
        Notifiable, Thumbnail, WithAttribute;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lastname',
        'middlename',
        'firstname',
        'phone',
        'email',
        'password',
        'status',
        'username',
        'referrer_id',
        'is_accept_condition',
        'country',
        'suspend_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'suspend_at' => 'datetime',
        'password' => 'hashed',
        'status' => UserStatus::class,
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return (new UserResource($this->load('roles')))
            ->toResponse(app('request'))->getData(true);
    }

    public function profile()
    {
        return $this->hasOne(
            UserProfile::class,
            'user_id'
        );
    }

    public function webServices(): HasMany
    {
        return $this->hasMany(
            WebService::class,
            'user_id'
        );
    }

    public function service(): Model|HasMany
    {
        return $this->webServices()
            ->where('name', StorageProvider::GOOGLE)
            ->first();
    }

    /**
     * Get user's videos
     */
    public function videos(): HasMany
    {
        return $this->hasMany(ChannelVideo::class, 'user_id');
    }

    /**
     * Get channels
     */
    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class, 'user_id');
    }

    public function scopeSearchs(Builder $builder, ?string $terms = null): Builder
    {

        return $builder->where(function ($builder) use ($terms) {
            collect(explode(' ', $terms))->filter()->each(function ($term) use ($builder) {
                $term = '%'.$term.'%';
                $builder->orWhere('firstname', 'like', $term)
                    ->orWhere('lastname', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('middlename', 'like', $term)
                    ->orWhere('id', $term);
            });
        });
    }
}
