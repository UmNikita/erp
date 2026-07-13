<?php

namespace App\CRM\DTO\OpenAPI\Pipeline;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PipelineRequest'
)]
final readonly class PipelineRequestDTO
{
    public function __construct(
        #[OA\Property(
            property: 'name',
            description: 'Название воронки',
            type: 'string',
            example: 'Продажи'
        )]
        public string $name
    ) {
    }
}