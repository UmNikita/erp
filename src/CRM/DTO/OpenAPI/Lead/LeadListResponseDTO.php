<?php

namespace App\CRM\DTO\OpenAPI\Lead;

use App\CRM\DTO\PaginationDTO;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LeadListResponse'
)]
final readonly class LeadListResponseDTO
{
    public function __construct(
        #[OA\Property(
            property: 'leads',
            description: 'Список всех лидов',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/Lead'
            )
        )]
        public array $leads,
        
        #[OA\Property(
            property: 'pagination',
            description: 'Параметры пагинации',
            ref: '#/components/schemas/Pagination'
        )]
        public ?PaginationDTO $pagination
    ) {
    }
}