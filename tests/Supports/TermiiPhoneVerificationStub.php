<?php

namespace Tests\Supports;

use App\Models\User;

class TermiiPhoneVerificationStub implements \App\Interfaces\PhoneVerificationInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(User $user, string $number): object|array
    {
        // TODO: Implement create() method.
    }

    /**
     * {@inheritDoc}
     */
    public function verify(User $user, string $code): bool
    {
        // TODO: Implement verify() method.
    }
}
