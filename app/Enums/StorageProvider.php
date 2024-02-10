<?php

namespace App\Enums;

use App\Traits\EnumOperation;

enum StorageProvider: string
{
    use EnumOperation;

    case LOCAL = 'local';
    case PUBLIC = 'public';
    case S3PRIVATE = 's3';
    case S3PUBLIC = 's3-public';
    case CLOUDINARY = 'cloudinary';
    case GOOGLE = 'google';
    case YOUTUBE = 'youtube';
    case VIMEO = 'video';
}
