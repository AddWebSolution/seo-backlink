<?php

namespace App\Modules\Keyword\Enums;

enum KeywordStatus: int
{
    case ACTIVE = 1;
    case INACTIVE = 2;

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'active',
            self::INACTIVE => 'inactive',
        };
    }
}

