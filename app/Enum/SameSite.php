<?php

declare(strict_types = 1);

namespace App\Enum;

enum SameSite: string
{
    case None = 'none';
    case Lax = 'lax';
    case Strict = 'strict';
}
