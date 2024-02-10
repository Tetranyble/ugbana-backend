<?php

namespace App\Enums;

use App\Traits\EnumOperation;

enum UserStatus: string
{
    use EnumOperation;

    case ACTIVE = 'ACTIVE';
    case INACTIVE = 'INACTIVE';
    case SUSPENDED = 'SUSPENDED';
    case WAITLIST = 'WAIT_LIST';
}
