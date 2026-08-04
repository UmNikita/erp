<?php
namespace App\CRM\DTO\Pipeline;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Pipeline'
)]
class PipelineDTO
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public int $id,

        #[OA\Property(type: 'string', example: 'Продажи')]
        public string $name,
    ) {}
}