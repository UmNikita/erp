<?php

namespace App\CRM\DTO\OpenAPI\Client;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ClientRequest',
    required: ['name']
)]
final readonly class ClientUpdateRequestDTO
{
    public function __construct(
        #[OA\Property(
            property: 'name',
            description: 'Название этапа',
            type: 'string',
            example: 'ООО «ТехноПарк»',
            nullable: true
        )]
        public ?string $name,

        #[OA\Property(
            property: 'inn',
            description: 'ИНН клиента',
            type: 'string',
            example: '7701234567',
            nullable: true
        )]
        public ?string $inn,

        #[OA\Property(
            property: 'field_of_activity',
            description: 'Сфера деятельности',
            type: 'string',
            example: 'ИТ, разработка ПО',
            nullable: true
        )]
        public ?string $field_of_activity,

        #[OA\Property(
            property: 'website',
            description: 'Сайт клиента',
            type: 'string',
            example: 'https://technopark.ru',
            nullable: true
        )]
        public ?string $website,

        #[OA\Property(
            property: 'phone',
            description: 'Телефон клиента',
            type: 'string',
            example: '84951234567',
            nullable: true
        )]
        public ?string $phone,

        #[OA\Property(
            property: 'email',
            description: 'Email клиента',
            type: 'string',
            example: 'info@technopark.ru',
            nullable: true
        )]
        public ?string $email,

        #[OA\Property(
            property: 'city',
            description: 'Город клиента',
            type: 'string',
            example: 'Москва',
            nullable: true
        )]
        public ?string $city,

        #[OA\Property(
            property: 'channel',
            description: 'Предпочитаемый канал клиента',
            type: 'string',
            example: 'Email',
            nullable: true
        )]
        public ?string $channel
    ) {
    }
}