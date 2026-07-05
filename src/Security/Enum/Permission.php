<?php

namespace App\Security\Enum;

enum Permission: string
{
    case ROOT_ACCESS = 'ROOT_ACCESS';
    case CRM_ACCESS = 'CRM_ACCESS';
    case REPOST_ACCESS = 'REPOST_ACCESS';
    case SITE_ACCESS = 'SITE_ACCESS';
    case ARTICLE_ACCESS = 'ARTICLE_ACCESS';
    case PARTNER_ACCESS = 'PARTNER_ACCESS';
    case STATISTIC_ACCESS = 'STATISTIC_ACCESS';
    case TASK_ACCESS = 'TASK_ACCESS';
    case CALL_ACCESS = 'CALL_ACCESS';
    case BASE_ACCESS = 'BASE_ACCESS';
    case AI_ACCESS = 'AI_ACCESS';
}