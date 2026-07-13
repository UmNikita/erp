<?php

namespace App\CRM\DTO\OpenAPI\Pipeline;

use OpenApi\Attributes as OA;
use App\CRM\DTO\PipelineDTO;

#[OA\Schema(
    schema: 'PipelineListResponse'
)]
final readonly class PipelineListResponseDTO
{
    /**
     * @param PipelineDTO[] $pipelines
     */
    
    public function __construct(
        #[OA\Property(
            property: 'pipelines',
            description: 'Список воронок',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/Pipeline'
            )
        )]
        public array $pipelines
    ) {
    }
}