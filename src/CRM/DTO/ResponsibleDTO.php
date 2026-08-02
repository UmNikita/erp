<?php
namespace App\CRM\DTO;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Responsible'
)]
class ResponsibleDTO
{
    public function __construct(

       #[OA\Property(type: 'integer', example: 1)]
        public int $id,

        #[OA\Property(type: 'string', example: 'Михаил П.')]
        public string $name,

        #[OA\Property(type: 'string', example: 'miha@mail.ru')]
        public string $email,
    ) {}
}