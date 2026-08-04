<?php

namespace App\CRM\DTO\OpenAPI\Stage;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'StageRequest'
)]
final readonly class StageRequestDTO
{
    public function __construct(
        #[OA\Property(
            property: 'name',
            description: 'Название этапа',
            type: 'string',
            example: 'Первичный контакт'
        )]
        #[Assert\NotBlank(message: 'Name required')]
        #[Assert\Length(min: 1, max: 50)]
        public ?string $name,

        #[OA\Property(
            property: 'color',
            description: 'UI-цвет этапа',
            type: 'string',
            example: '#5340b6'
        )]
        #[Assert\NotBlank(message: 'Color required')]
        #[Assert\Regex(
            pattern: '/^#[0-9A-Fa-f]{6}$/',
            message: 'Color must be in format HEX (#FFFFFF)'
        )]
        public ?string $color,

        #[OA\Property(
            property: 'pipeline_id',
            description: 'ID воронки',
            type: 'integer',
            example: '4'
        )]
        #[Assert\NotBlank(message: 'Pipeline_id required')]
        public ?int $pipeline_id
    ) {
    }
}