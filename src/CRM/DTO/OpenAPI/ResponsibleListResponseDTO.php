<?php

namespace App\CRM\DTO\OpenAPI;

use OpenApi\Attributes as OA;
use App\CRM\DTO\ResponsibleDTO;

#[OA\Schema(
    schema: 'ResponsibleListResponse'
)]
final readonly class ResponsibleListResponseDTO
{
    /**
     * @param ResponsibleDTO[] $responsibles
     */
    
    public function __construct(
        #[OA\Property(
            property: 'responsibles',
            description: 'Список менеджеров',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/Responsible'
            )
        )]
        public array $responsibles
    ) {
    }
}