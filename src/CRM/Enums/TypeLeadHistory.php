<?php

namespace App\CRM\Enums;

enum TypeLeadHistory: string
{
    case UPDATED  = 'updated';
    case FINISH = 'finish';
    case STAGE_CHANGED = 'stage_changed';
}