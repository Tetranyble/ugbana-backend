<?php

namespace App\Interface;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;

interface PhoneVerificationInterface
{
    /**
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function create(User $user, string $number): object|array;

    /**
     * @throws TwilioException
     * @throws ValidationException
     */
    public function verify(User $user, string $code): bool;
}
