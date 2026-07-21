<?php

namespace App\CRM\Enums;

enum LeadStatus: string
{
    case ACTIVE = 'active';
    case WON = 'won';
    case LOST = 'lost';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}