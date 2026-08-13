<?php
namespace App\CRM\DTO\Client;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ClientPagination'
)]
class ClientPaginationDTO
{
    public function __construct(

        #[OA\Property(type: 'integer', example: 1)]
        public int $page,

        #[OA\Property(type: 'integer', example: 10)]
        public int $limit,

        #[OA\Property(type: 'integer', example: 10)]
        public int $count,

        #[OA\Property(type: 'integer', example: 140)]
        public int $allCount,

    ) {}
}