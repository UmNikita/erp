<?php
namespace App\CRM\DTO\Stage;

use App\CRM\DTO\Pipeline\PipelineDTO;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LeadStage'
)]
class LeadStageDTO
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 3)]
        public int $id,

        #[OA\Property(type: 'string', example: 'Переговоры')]
        public string $name,

        #[OA\Property(
            property: 'pipeline',
            ref: '#/components/schemas/Pipeline'
        )]
        public PipelineDTO $pipeline,
    ) {}
}