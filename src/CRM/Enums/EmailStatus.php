<?php

namespace App\CRM\Enums;

enum EmailStatus: string
{
    case SUCCESS = 'success';
    case FAIL = 'fail';
}