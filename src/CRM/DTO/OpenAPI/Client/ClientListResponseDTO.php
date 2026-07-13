<?php

namespace App\CRM\DTO\OpenAPI\Client;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ClientListResponse'
)]
final readonly class ClientListResponseDTO
{
    public function __construct(
        #[OA\Property(
            property: 'clients',
            description: 'Список всех лидов',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/Client'
            )
        )]
        public array $client
    ) {
    }
}