<?php
namespace App\CRM\DTO\Client;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ClientDetail',
    allOf: [
        new OA\Schema(ref: '#/components/schemas/Client')
    ]
)]
class ClientDetailDTO extends ClientDTO
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
        public int $ltv,

        #[OA\Property(type: 'integer', example: '25000')]
        public int $average_cheque,

        #[OA\Property(type: 'integer', example: '3')]
        public int $count_leads,

        #[OA\Property(type: 'integer', example: '75000')]
        public int $amount_sum_leads,

        #[OA\Property(
            property: 'contacts',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/Contact'
            )
        )]
        public ?array $contacts
    ) {
        parent::__construct(
            $id,
            $name,
            $inn,
            $field_of_activity,
            $website,
            $phone,
            $email,
            $city,
            $channel,
            $date_create,
        );
    }
}