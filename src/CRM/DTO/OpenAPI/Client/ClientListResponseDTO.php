<?php

namespace App\CRM\DTO\OpenAPI\Client;

use App\CRM\DTO\Client\ClientPaginationDTO;
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
        public array $clients,
        
        #[OA\Property(
            property: 'pagination',
            description: 'Параметры пагинации',
            ref: '#/components/schemas/ClientPagination'
        )]
        public ?ClientPaginationDTO $pagination
    ) {
    }
}