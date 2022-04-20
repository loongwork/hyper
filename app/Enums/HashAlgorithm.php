<?php

declare(strict_types=1);

namespace App\Enums;

enum HashAlgorithm: int
{
    case MD5 = 1;
    case PHPBB3 = 2;
    case SHA1 = 3;
    case SHA256 = 5;
    case BCRYPT = 7;
}
