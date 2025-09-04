<?php

namespace App\Enums;

enum UserRole: int
{
    case SUPERADMIN = 1;
    case SUBADMIN   = 2;
    case CLIENT     = 3;
    case STAFF      = 4;
}
