<?php

namespace App\CRM\DTO\OpenAPI\LeadMessages;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LeadMessagesListResponse'
)]
final readonly class LeadMessagesListResponseDTO
{
    public function __construct(
        
        #[OA\Property(type: 'integer', example: 1)]
        public ?int $limit,

        #[OA\Property(type: 'integer', example: 23)]
        public ?int $before_id,

        #[OA\Property(
            property: 'messages',
            description: 'Список всех сообщений сделки',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/Message'
            )
        )]
        public array $leadMessages

    ) {
    }
}