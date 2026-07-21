<?php

namespace App\CRM\Exception;

class InvalidRequestException extends \RuntimeException
{
    public function __construct(
        private array $errors
    ) {
        parent::__construct('Invalid request');
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}