<?php
namespace App\CRM\DTO\Client;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Client'
)]
class ClientDTO
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public int $id,

        #[OA\Property(type: 'string', example: 'ООО «ТехноПарк»')]
        public string $name,

        #[OA\Property(type: 'string', example: '7701234567')]
        public ?string $inn,

        #[OA\Property(type: 'string', example: 'ИТ, разработка ПО')]
        public ?string $field_of_activity,

        #[OA\Property(type: 'string', example: 'https://technopark.ru')]
        public ?string $website,

        #[OA\Property(type: 'string', example: '84951234567')]
        public ?string $phone,

        #[OA\Property(type: 'string', example: 'info@technopark.ru')]
        public ?string $email,

        #[OA\Property(type: 'string', example: 'Москва')]
        public ?string $city,

        #[OA\Property(type: 'string', example: 'Email')]
        public ?string $channel,

        #[OA\Property(
            type: 'string', 
            format: 'date-time', 
            example: '2026-01-01T00:00:00+00:00'
        )]
        public \DateTime $date_create

    ) {}
}