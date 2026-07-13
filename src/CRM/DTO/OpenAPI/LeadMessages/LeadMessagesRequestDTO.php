<?php

namespace App\CRM\DTO\OpenAPI\LeadMessages;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LeadMessagesRequest',
    required: ['name', 'stage_id']
)]
final readonly class LeadMessagesRequestDTO
{
    public function __construct(
        #[OA\Property(
            property: 'name',
            description: 'Текст сообщения',
            type: 'string',
            example: 'Добавила в КП блок по интеграции и примеры отчётов. Проверьте, пожалуйста.'
        )]
        public string $message,

        #[OA\Property(
            property: 'user_id',
            description: 'ID пользователя',
            type: 'integer',
            example: '6'
        )]
        public string $user_id,

        #[OA\Property(
            property: 'lead_id',
            description: 'ID сделки',
            type: 'integer',
            example: '13'
        )]
        public string $lead_id
    ) {
    }
}