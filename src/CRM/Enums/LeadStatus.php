<?php

namespace App\CRM\Enums;

enum LeadStatus: string
{
    case ACTIVE = 'active';
    case WON = 'won';
    case LOST = 'lost';
}