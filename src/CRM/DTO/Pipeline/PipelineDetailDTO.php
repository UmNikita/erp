<?php
namespace App\CRM\DTO\Pipeline;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PipelineDetail'
)]
class PipelineDetailDTO
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public int $id,

        #[OA\Property(type: 'string', example: 'Продажи')]
        public string $name,

        #[OA\Property(
            property: 'stages',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/Stage'
            )
        )]
        public array $stages
    ) {}
}