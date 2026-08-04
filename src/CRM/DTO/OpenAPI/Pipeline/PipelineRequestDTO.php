<?php

namespace App\CRM\DTO\OpenAPI\Pipeline;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

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
        #[Assert\NotBlank(message: 'Name required')]
        #[Assert\Length(
            min: 1,
            max: 50
        )]
        public string $name
    ) {
    }
}