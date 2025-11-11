<?php

namespace App\Enums;

enum UserRole: int
{
    case SUPERADMIN = 1;
    case CLIENT     = 2;
}
