<?php

namespace App\CRM\DTO\OpenAPI\Stage;

use OpenApi\Attributes as OA;

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
        public ?string $name,

        #[OA\Property(
            property: 'color',
            description: 'UI-цвет этапа',
            type: 'string',
            example: '#5340b6',
            nullable: true
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
}