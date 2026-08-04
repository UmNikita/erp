<?php

namespace App\CRM\DTO\OpenAPI\Contact;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

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
        #[Assert\NotBlank(message: 'Name required')]
        #[Assert\Length(
            min: 1, max: 100
        )]
        public string $name,

        #[OA\Property(
            property: 'client_id',
            description: 'ID клиента',
            type: 'integer',
            example: '1',
            nullable: true
        )]
        public ?int $client_id,

        #[OA\Property(
            property: 'secondname',
            description: 'Фамилия',
            type: 'string',
            example: 'Иванова',
            nullable: true
        )]
        #[Assert\NotBlank(message: 'Secondname required')]
        #[Assert\Length(
            min: 1, max: 100
        )]
        public ?string $secondname,

        #[OA\Property(
            property: 'thirdname',
            description: 'Отчество',
            type: 'string',
            example: 'Ивановна',
            nullable: true
        )]
        #[Assert\Length(max: 100)]
        public ?string $thirdname,

        #[OA\Property(
            property: 'position',
            description: 'Должность',
            type: 'string',
            example: 'Руководитель отдела продаж',
            nullable: true
        )]
        #[Assert\Length(max: 100)]
        public ?string $position,
        
        #[OA\Property(
            property: 'phone',
            description: 'Номер телефона',
            type: 'string',
            example: '+7 (916) 987-65-43',
            nullable: true
        )]
        #[Assert\Length(max: 50)]
        #[Assert\Regex(
            pattern: '/^\+?[\d\s\-()]+$/',
            message: 'The phone must contain only numbers.'
        )]
        public ?string $phone,

        #[OA\Property(
            property: 'email',
            description: 'Почта',
            type: 'string',
            example: 'anna.ivanova@technopark.ru',
            nullable: true
        )]
        #[Assert\Length(max: 255)]
        #[Assert\Email(
            message: 'Invalid email'
        )]
        public ?string $email,

        #[OA\Property(
            property: 'messenger',
            description: 'Мессенджер',
            type: 'string',
            example: 'Telegram: @anna_ivanova',
            nullable: true
        )]
        #[Assert\Length(max: 100)]
        public ?string $messenger,
        
        #[OA\Property(
            property: 'note',
            description: 'Примечание',
            type: 'string',
            example: 'Основное лицо, принимает решение по CRM',
            nullable: true
        )]
        #[Assert\Length(max: 255)]
        public ?string $note,
    ) {
    }
}