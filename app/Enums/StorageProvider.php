<?php

namespace App\Enums;

use App\Traits\EnumOperation;

enum StorageProvider: string
{
    use EnumOperation;

    case LOCAL = 'local';
    case S3PRIVATE = 's3-private';

    case S3PUBLIC = 's3';
    case CLOUDINARY = 'cloudinary';
    case GOOGLE = 'google';
    case YOUTUBE = 'youtube';
    case VIMEO = 'vimeo';

    case FTP = 'ftp';
    case NONE = 'none';
}
