<?php
namespace App\CRM\DTO;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Kanban'
)]
class KanbanDTO
{
    public function __construct(

        #[OA\Property(type: 'integer', example: 1)]
        public int $leadsCount,
        
        #[OA\Property(type: 'integer', example: 75000)]
        public int $moneyAmount,
        
        #[OA\Property(
            property: 'stages',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/StageKanban'
            )
        )]
        public array $stages
    ) {}
}