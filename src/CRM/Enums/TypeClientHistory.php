<?php

namespace App\CRM\Enums;

enum TypeClientHistory: string
{
    case UPDATED = 'updated';
    case LEAD_CREATED = 'lead_created';
    case LEAD_APPOINTED = 'lead_appointed';
    case LEAD_FINISH = 'lead_finish';
    case EMAIL_SENDED = 'email_sended';
    case KP_SENDED = 'kp_sended';
    case CONTACT_CREATED = 'contact_created';
    case CONTACT_UPDATED = 'contact_updated';
    case CONTACT_DELETE = 'contact_delete';
}