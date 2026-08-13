<?php
namespace App\CRM\DTO;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'History'
)]
class HistoryDTO
{
    public function __construct(
        #[OA\Property(type: 'string', example: 'Объект удален!')]
        public string $message,

        #[OA\Property(type: 'string', example: 'Алексей')]
        public string $managerName,

        #[OA\Property(
            type: 'string', 
            format: 'date-time', 
            example: '2026-01-01T00:00:00+00:00'
        )]
        public \DateTime $date

    ) {}
}