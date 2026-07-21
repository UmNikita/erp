<?php
namespace App\CRM\DTO\Lead;

use App\CRM\Enums\LeadStatus;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Lead'
)]
class LeadDTO
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public int $id,

        #[OA\Property(type: 'string', example: 'РегионПлюс')]
        public string $name,

        #[OA\Property(type: 'number', format: 'float', example: 75000)]
        public float $budget,

        #[OA\Property(type: 'string', example: 'CRM: лицензии + внедрение')]
        public ?string $product,

        #[OA\Property(type: 'string', example: 'Холодный звонок')]
        public ?string $source,

        #[OA\Property(type: 'string', example: 'Презентаци решения')]
        public ?string $next_action,

        #[OA\Property(
            type: 'string', 
            format: 'date-time', 
            example: '2026-01-01T00:00:00+00:00'
        )]
        public \DateTime $dateStart,

        #[OA\Property(
            type: 'string',
            format: 'date-time',
            example: '2026-02-01T00:00:00+00:00'
        )]
        public ?\DateTime $date_next_action,

        #[OA\Property(type: 'string', example: 'Клиент рассматривает интеграцию в 1С')]
        public ?string $comment,

        #[OA\Property(
            type: 'string',
            enum: ['active', 'won', 'lost'],
            example: 'active'
        )]
        public LeadStatus $status,

        #[OA\Property(type: 'integer', example: '3')]
        public ?int $stage_id,
        
        #[OA\Property(type: 'integer', example: '2')]
        public ?int $client_id

    ) {}
}