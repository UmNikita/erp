<?php
namespace App\CRM\DTO\Lead;

use App\CRM\DTO\Client\ClientDetailDTO;
use App\CRM\Enums\LeadStatus;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LeadDetail',
    allOf: [
        new OA\Schema(ref: '#/components/schemas/Lead')
    ]
)]
class LeadDetailDTO extends LeadDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public float $budget,
        public ?string $product,
        public ?string $source,
        public ?string $next_action,
        public \DateTime $dateStart,
        public ?\DateTime $date_next_action,
        public ?string $comment,
        public LeadStatus $status,
        public ?int $stage_id,
        public ?int $client_id,

        #[OA\Property(
            property: 'client',
            ref: '#/components/schemas/ClientDetail'
        )]
        public ?ClientDetailDTO $client,
        
    ) {
        parent::__construct(
            $id,
            $name,
            $budget,
            $product,
            $source,
            $next_action,
            $dateStart,
            $date_next_action,
            $comment,
            $status,
            $stage_id,
            $client_id
        );
    }
}