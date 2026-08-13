<?php
namespace App\CRM\DTO\Client;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'EmailKPRequest'
)]
class EmailKPRequestDTO
{
    public function __construct(
        #[OA\Property(type: 'string', example: 1)]
        public string $manager_name,

        #[OA\Property(type: 'string', example: '8701234567')]
        public string $manager_phone,

        #[OA\Property(type: 'number', example: '1')]
        public ?int $contact_id

    ) {}
}