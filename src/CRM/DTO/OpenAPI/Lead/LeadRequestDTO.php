<?php

namespace App\CRM\DTO\OpenAPI\Lead;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LeadRequest',
    required: ['name', 'stage_id']
)]
final readonly class LeadRequestDTO
{
    public function __construct(
        #[OA\Property(
            property: 'name',
            description: 'Название этапа',
            type: 'string',
            example: 'РегионПлюс'
        )]
        public string $name,

        #[OA\Property(
            property: 'stage_id',
            description: 'ID этапа',
            type: 'integer',
            example: '1'
        )]
        public string $stage_id,

        #[OA\Property(
            property: 'budget',
            description: 'Сумма сделки',
            type: 'number',
            format: 'float',
            example: 75000,
            nullable: true
        )]
        public float $budget,

        #[OA\Property(
            property: 'product',
            description: 'Название продукта',
            type: 'string',
            example: 'CRM: лицензии + внедрение',
            nullable: true
        )]
        public string $product,

        #[OA\Property(
            property: 'source',
            description: 'Источник',
            type: 'string',
            example: 'Холодный звонок',
            nullable: true
        )]
        public string $source,

        #[OA\Property(
            property: 'next_action',
            description: 'Следующее действие',
            type: 'string',
            example: 'Презентаци решения',
            nullable: true
        )]
        public string $next_action,

        #[OA\Property(
            property: 'date_next_action',
            description: 'Дата следующего действия',
            type: 'string',
            example: '01.01.2026',
            nullable: true
        )]
        public \DateTime $date_next_action,

        #[OA\Property(
            property: 'comment',
            description: 'Комментарий',
            type: 'string',
            example: 'Клиент рассматривает интеграцию в 1С',
            nullable: true
        )]
        public string $comment
    ) {
    }
}