<?php
namespace App\CRM\DTO\Client;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Contact'
)]
class ContactDTO
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public int $id,

        #[OA\Property(type: 'string', example: 'Анна')]
        public string $name,

        #[OA\Property(type: 'string', example: 'Иванова')]
        public string $secondname,

        #[OA\Property(type: 'string', example: 'Ивановна')]
        public string $thirdname,

        #[OA\Property(type: 'string', example: 'Руководитель отдела продаж')]
        public string $position,
        
        #[OA\Property(type: 'string', example: '+7 (916) 987-65-43')]
        public string $phone,

        #[OA\Property(type: 'string', example: 'anna.ivanova@technopark.ru')]
        public string $email,

        #[OA\Property(type: 'string', example: 'Telegram: @anna_ivanova')]
        public string $messenger,
        
        #[OA\Property(type: 'string', example: 'Основное лицо, принимает решение по CRM')]
        public string $note,

        #[OA\Property(
            type: 'string', 
            format: 'date-time', 
            example: '2026-01-01T00:00:00+00:00'
        )]
        public \DateTime $date_create,

    ) {}
}