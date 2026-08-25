<?php

namespace App\CRM\DTO\OpenAPI\LeadMessages;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'LeadMessagesRequest',
    required: ['name', 'stage_id']
)]
final readonly class LeadMessagesRequestDTO
{
    public function __construct(
        #[OA\Property(
            property: 'message',
            description: 'Текст сообщения',
            type: 'string',
            example: 'Добавила в КП блок по интеграции и примеры отчётов. Проверьте, пожалуйста.'
        )]
        #[Assert\NotBlank(message: 'Message required')]
        #[Assert\Length(
            min: 1, max: 4096
        )]
        public string $message,

        #[OA\Property(
            property: 'lead_id',
            description: 'ID сделки',
            type: 'integer',
            example: '13'
        )]
        #[Assert\NotBlank(message: 'lead_id required')]
        public int $lead_id
    ) {
    }
}