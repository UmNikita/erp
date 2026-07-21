<?php

namespace App\CRM\DTO\OpenAPI\Stage;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'StageRequestEdit'
)]
final readonly class StageRequestEditDTO
{
    public function __construct(

        #[OA\Property(
            property: 'name',
            description: 'Название этапа',
            type: 'string',
            example: 'Первичный контакт',
            nullable: true
        )]
        #[Assert\Length(max: 50)]
        public ?string $name,

        #[OA\Property(
            property: 'color',
            description: 'UI-цвет этапа',
            type: 'string',
            example: '#5340b6',
            nullable: true
        )]
        #[Assert\Regex(
            pattern: '/^#[0-9A-Fa-f]{6}$/',
            message: 'Color must be in format HEX (#FFFFFF)'
        )]
        public ?string $color,

        #[OA\Property(
            property: 'pipeline_id',
            description: 'ID воронки',
            type: 'integer',
            example: '4',
            nullable: true
        )]
        public ?int $pipeline_id
    ) {
    }

    public function isEmpty(): bool
    {
        return $this->name === null&& $this->color === null && $this->pipeline_id === null;
    }
}