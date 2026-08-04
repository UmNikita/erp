<?php

namespace App\CRM\DTO\OpenAPI\Pipeline;

use OpenApi\Attributes as OA;
use App\CRM\DTO\Pipeline\PipelineDetailDTO;

#[OA\Schema(
    schema: 'PipelineListDetailResponse'
)]
final readonly class PipelineListDetailResponseDTO
{
    /**
     * @param PipelineDetailDTO[] $pipelines
     */
    
    public function __construct(
        #[OA\Property(
            property: 'pipelines',
            description: 'Список воронок',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/PipelineDetail'
            )
        )]
        public array $pipelines
    ) {
    }
}