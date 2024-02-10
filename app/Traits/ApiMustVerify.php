<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\DB;

trait ApiMustVerify
{
    /**
     * Mark the given user's email as verified.
     */
    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Mark the given user's email as verified.
     */
    public function markPhoneAsVerified(): bool
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function hasVerifiedPhone(): bool
    {
        return ! is_null($this->phone_verified_at);
    }

    /**
     * @throws \Exception
     */
    public function phoneVerification(): bool
    {
        $code = random_int(1000, 9999);

        return $this->forceFill([
            'verification_code' => $code,
        ])->save();
    }

    public function preEmailVerification()
    {
        $code = static::VERIFICATION_CODE();
        if (! DB::table('password_reset_tokens')->where('email', $this->email)->first()) {
            DB::table('password_reset_tokens')->insert([
                'email' => $this->email,
                'token' => $code,
                'created_at' => now(),
            ]);
        } else {
            DB::table('password_reset_tokens')
                ->where('email', $this->email)
                ->update([
                    'email' => $this->email,
                    'token' => $code,
                    'created_at' => now(),
                ]);
        }

        return $code;
    }

    public static function VERIFICATION_CODE()
    {
        return str_pad(random_int(1, 9999), 6, '0', STR_PAD_LEFT);
    }
}
