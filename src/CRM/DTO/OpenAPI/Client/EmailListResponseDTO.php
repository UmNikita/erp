<?php

namespace App\CRM\DTO\OpenAPI\Client;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'EmailListResponse'
)]
final readonly class EmailListResponseDTO
{
    public function __construct(
        #[OA\Property(
            property: 'emails',
            description: 'Список всех лидов',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/EmailRecord'
            )
        )]
        public array $emails
    ) {
    }
}