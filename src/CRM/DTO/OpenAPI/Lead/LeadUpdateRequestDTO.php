<?php

namespace App\CRM\DTO\OpenAPI\Lead;

use App\CRM\Enums\LeadStatus;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'LeadUpdateRequest'
)]
final readonly class LeadUpdateRequestDTO
{
    public function __construct(
        #[OA\Property(
            property: 'name',
            description: 'Название этапа',
            type: 'string',
            example: 'РегионПлюс',
            nullable: true
        )]
        #[Assert\Length(max: 50)]
        public ?string $name,

        #[OA\Property(
            property: 'stage_id',
            description: 'ID этапа',
            type: 'integer',
            example: '1',
            nullable: true
        )]
        public ?int $stage_id,

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
        #[Assert\Length(max: 50)]
        public ?string $product,

        #[OA\Property(
            property: 'source',
            description: 'Источник',
            type: 'string',
            example: 'Холодный звонок',
            nullable: true
        )]
        #[Assert\Length(max: 50)]
        public ?string $source,

        #[OA\Property(
            property: 'next_action',
            description: 'Следующее действие',
            type: 'string',
            example: 'Презентаци решения',
            nullable: true
        )]
        #[Assert\Length(max: 50)]
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
        #[Assert\Length(max: 255)]
        public ?string $comment,

        #[OA\Property(
            property: 'status',
            description: 'Статус',
            type: 'string',
            example: 'won',
            nullable: true
        )]
        #[Assert\Choice(
            callback: [LeadStatus::class, 'values'],
            message: 'Invalid status'
        )]
        public ?string $status,
    ) {
    }

    public function isEmpty(): bool
    {
        return $this->name === null && $this->stage_id === null && $this->client_id === null && $this->budget === null && $this->product === null && $this->source === null && $this->next_action === null && $this->date_next_action === null && $this->comment === null && $this->status === null;
    }
}