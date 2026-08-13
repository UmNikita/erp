<?php

namespace App\CRM\DTO\Client;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'EmailRecord'
)]
class EmailRecordDTO
{
    public function __construct(
        #[OA\Property(type: 'string', example: 'Объект удален!')]
        public string $title,

        #[OA\Property(type: 'string', example: 'Алексей')]
        public string $managerName,

        #[OA\Property(
            type: 'string', 
            format: 'date-time', 
            example: '2026-01-01T00:00:00+00:00'
        )]
        public \DateTime $date,

        #[OA\Property(type: 'bool', example: true)]
        public bool $isSuccess,

    ) {}
}