<?php
namespace App\CRM\DTO\Stage;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'StageGet'
)]
class StageGetDTO
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public int $id,

        #[OA\Property(type: 'string', example: 'Первичный контакт')]
        public string $name,

        #[OA\Property(type: 'string', example: '#5340b6')]
        public string $color,

        #[OA\Property(type: 'integer', example: '2')]
        public string $sequence
    ) {}
}