<?php

namespace App\Enums;

use App\Traits\EnumOperation;

enum DocumentStatus: string
{
    use EnumOperation;

    case PUBLISHED = 'PUBLISHED';
    case WITHHELD = 'WITHHELD';
    case PENDING = 'PENDING';
}
