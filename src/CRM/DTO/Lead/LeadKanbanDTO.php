<?php
namespace App\CRM\DTO\Lead;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LeadKanban'
)]
class LeadKanbanDTO
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public int $id,

        #[OA\Property(type: 'string', example: 'РегионПлюс')]
        public string $name,

         #[OA\Property(type: 'string', format: 'date-time', example: '2026-01-01T00:00:00+00:00')]
        public string $date,

        #[OA\Property(type: 'string', example: 'Мария Кузнецова')]
        public string $client,

        #[OA\Property(type: 'string', example: 'Михаил П.')]
        public string $manager,

        #[OA\Property(type: 'number', format: 'float', example: 75000)]
        public float $moneyAmount,
    ) {}
}