<?php

namespace App\Traits;

use App\Events\ApiRegistered;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

trait WithAttribute
{
    public function getReferralAttribute()
    {
        return route('v1.signup', [
            'referrer' => $this->username,
        ]);
    }

    public function referredBy(string|User|null $username = null): bool|ModelNotFoundException
    {
        return ($username instanceof User) ?
            $this->update([
                'referrer_id' => $username->id,
            ]) : (isset($username) && $this->update([
                'referrer_id' => User::where('username', $username)->firstOrFail()?->id,
            ]));

    }

    /**
     * Get fullname
     */
    public function getFullnameAttribute(): string
    {
        return $this->firstname.' '.$this->lastname;
    }

    protected static function bootWithAttribute()
    {
        static::creating(function ($model) {
            static::resolveUsername($model);
        });
        //        static::created(function ($model) {
        //            event(new ApiRegistered($model));
        //        });
    }

    protected static function resolveUsername(Model $model): void
    {
        if (self::whereUsername($model->username)->exists()) {

            $firstname = Str::slug($model->firstname);
            $lastname = Str::slug($model->lastname);

            $username = $firstname.'.'.$lastname;
            $i = 0;
            while (self::whereUsername($username)->exists()) {
                $i++;
                $username = $firstname.'.'.$lastname.$i;
            }
            $model->fill(['username' => $username])
                ->save();
        }
    }
}
