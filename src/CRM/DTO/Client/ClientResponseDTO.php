<?php
namespace App\CRM\DTO\Client;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Client'
)]
class ClientResponseDTO extends ClientDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $inn,
        public ?string $field_of_activity,
        public ?string $website,
        public ?string $phone,
        public ?string $email,
        public ?string $city,
        public ?string $channel,
        public \DateTime $date_create,

        #[OA\Property(type: 'integer', example: '125000')]
        public int $leads_count,

        #[OA\Property(type: 'integer', example: '125000')]
        public int $leads_amount,

    ) {}
}