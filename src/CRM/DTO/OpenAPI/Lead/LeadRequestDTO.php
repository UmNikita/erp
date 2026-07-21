<?php

namespace App\CRM\DTO\OpenAPI\Lead;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'LeadRequest',
    required: ['name', 'stage_id']
)]
readonly class LeadRequestDTO
{
    public function __construct(
        #[OA\Property(
            property: 'name',
            description: 'Название этапа',
            type: 'string',
            example: 'РегионПлюс'
        )]
        #[Assert\NotBlank(message: 'Name required')]
        #[Assert\Length(
            min: 1, max: 50
        )]
        public string $name,

        #[OA\Property(
            property: 'stage_id',
            description: 'ID этапа',
            type: 'integer',
            example: '1'
        )]
        #[Assert\NotBlank(message: 'stage_id required')]
        public int $stage_id,

        #[OA\Property(
            property: 'client_id',
            description: 'ID клиента',
            type: 'integer',
            example: '1',
            nullable: true
        )]
        public ?int $client_id,

        #[OA\Property(
            property: 'budget',
            description: 'Сумма сделки',
            type: 'number',
            format: 'float',
            example: 75000,
            nullable: true
        )]
        public ?float $budget,

        #[OA\Property(
            property: 'product',
            description: 'Название продукта',
            type: 'string',
            example: 'CRM: лицензии + внедрение',
            nullable: true
        )]
        #[Assert\Length(
            min: 1, max: 50
        )]
        public ?string $product,

        #[OA\Property(
            property: 'source',
            description: 'Источник',
            type: 'string',
            example: 'Холодный звонок',
            nullable: true
        )]
        #[Assert\Length(
            min: 1, max: 50
        )]
        public ?string $source,

        #[OA\Property(
            property: 'next_action',
            description: 'Следующее действие',
            type: 'string',
            example: 'Презентаци решения',
            nullable: true
        )]
        #[Assert\Length(
            min: 1, max: 50
        )]
        public ?string $next_action,

        #[OA\Property(
            property: 'date_next_action',
            description: 'Дата следующего действия',
            type: 'string',
            example: '01.01.2026',
            nullable: true
        )]
        public ?\DateTime $date_next_action,

        #[OA\Property(
            property: 'comment',
            description: 'Комментарий',
            type: 'string',
            example: 'Клиент рассматривает интеграцию в 1С',
            nullable: true
        )]
        #[Assert\Length(
            min: 1, max: 255
        )]
        public ?string $comment
    ) {
    }
}