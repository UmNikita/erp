<?php

namespace App\CRM\DTO\OpenAPI\Client;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

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
        #[Assert\Length(max: 50)]
        public ?string $name,

        #[OA\Property(
            property: 'inn',
            description: 'ИНН клиента',
            type: 'string',
            example: '7701234567',
            nullable: true
        )]
        #[Assert\Length(max: 12)]
        #[Assert\Regex(
            pattern: '/^\d+$/',
            message: 'The INN must contain only numbers.'
        )]
        public ?string $inn,

        #[OA\Property(
            property: 'field_of_activity',
            description: 'Сфера деятельности',
            type: 'string',
            example: 'ИТ, разработка ПО',
            nullable: true
        )]
        #[Assert\Length(max: 50)]
        public ?string $field_of_activity,

        #[OA\Property(
            property: 'website',
            description: 'Сайт клиента',
            type: 'string',
            example: 'https://technopark.ru',
            nullable: true
        )]
        #[Assert\Length(max: 50)]
        #[Assert\Url(
            message: 'Invalid URL'
        )]
        public ?string $website,

        #[OA\Property(
            property: 'phone',
            description: 'Телефон клиента',
            type: 'string',
            example: '84951234567',
            nullable: true
        )]
        #[Assert\Length(max: 50)]
        #[Assert\Regex(
            pattern: '/^\d+$/',
            message: 'The phone must contain only numbers.'
        )]
        public ?string $phone,

        #[OA\Property(
            property: 'email',
            description: 'Email клиента',
            type: 'string',
            example: 'info@technopark.ru',
            nullable: true
        )]
        #[Assert\Length(max: 255)]
        #[Assert\Email(
            message: 'Invalid email'
        )]
        public ?string $email,

        #[OA\Property(
            property: 'city',
            description: 'Город клиента',
            type: 'string',
            example: 'Москва',
            nullable: true
        )]
        #[Assert\Length(max: 50)]
        public ?string $city,

        #[OA\Property(
            property: 'channel',
            description: 'Предпочитаемый канал клиента',
            type: 'string',
            example: 'Email',
            nullable: true
        )]
        #[Assert\Length(max: 50)]
        public ?string $channel
    ) {}

    public function isEmpty() {
        return $this->name === null && $this->inn === null && $this->field_of_activity === null && $this->website === null && $this->phone === null && $this->email === null && $this->city === null && $this->channel === null;
    }
}