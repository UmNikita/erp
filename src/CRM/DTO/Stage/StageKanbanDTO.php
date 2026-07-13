<?php
namespace App\CRM\DTO\Stage;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'StageKanban'
)]
class StageKanbanDTO
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public int $id,

        #[OA\Property(type: 'string', example: 'Новый лид')]
        public string $name,

        #[OA\Property(type: 'string', example: '#78BC61')]
        public string $color,

        #[OA\Property(type: 'integer', example: 4)]
        public int $leadCount,

        #[OA\Property(type: 'integer', example: 75000)]
        public int $moneyAmount,

        #[OA\Property(
            property: 'leads',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/LeadKanban'
            )
        )]
        public array $leads
    ) {}
}