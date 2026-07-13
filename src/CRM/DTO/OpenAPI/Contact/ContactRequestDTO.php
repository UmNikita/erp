<?php

namespace App\CRM\DTO\OpenAPI\Contact;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ContactRequest',
    required: ['name']
)]
final readonly class ContactRequestDTO
{
    public function __construct(
        #[OA\Property(
            property: 'name',
            description: 'Имя',
            type: 'string',
            example: 'Анна'
        )]
        public string $name,

        #[OA\Property(
            property: 'secondname',
            description: 'Фамилия',
            type: 'string',
            example: 'Иванова',
            nullable: true
        )]
        public string $secondname,

        #[OA\Property(
            property: 'thirdname',
            description: 'Отчество',
            type: 'string',
            example: 'Ивановна',
            nullable: true
        )]
        public string $thirdname,

        #[OA\Property(
            property: 'position',
            description: 'Должность',
            type: 'string',
            example: 'Руководитель отдела продаж',
            nullable: true
        )]
        public string $position,
        
        #[OA\Property(
            property: 'phone',
            description: 'Номер телефона',
            type: 'string',
            example: '+7 (916) 987-65-43',
            nullable: true
        )]
        public string $phone,

        #[OA\Property(
            property: 'email',
            description: 'Почта',
            type: 'string',
            example: 'anna.ivanova@technopark.ru',
            nullable: true
        )]
        public string $email,

        #[OA\Property(
            property: 'messenger',
            description: 'Мессенджер',
            type: 'string',
            example: 'Telegram: @anna_ivanova',
            nullable: true
        )]
        public string $messenger,
        
        #[OA\Property(
            property: 'note',
            description: 'Примечание',
            type: 'string',
            example: 'Основное лицо, принимает решение по CRM',
            nullable: true
        )]
        public string $note,
    ) {
    }
}