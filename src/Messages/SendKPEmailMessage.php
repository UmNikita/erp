<?php

namespace App\Messages;

final class SendKPEmailMessage
{
    public function __construct(
        public readonly int $clientId,
        public readonly ?int $contactId,
        public readonly int $managerId,
        public readonly string $managerName,
        public readonly string $managerPhone,
    ) {}
}