<?php

namespace App\CRM\DTO\OpenAPI\Integration;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'IntegrationListResponse'
)]
final readonly class IntegrationListResponseDTO
{
    public function __construct(
        #[OA\Property(
            property: 'integrations',
            description: 'Список всех интеграций',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/Integration'
            )
        )]
        public array $integrations
    ) {
    }
}