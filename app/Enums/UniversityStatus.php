<?php

namespace App\Enums;

enum UniversityStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Faol',
            self::INACTIVE => 'Nofaol'
        };
    }
}
