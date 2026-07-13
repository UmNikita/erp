<?php

namespace App\CRM\DTO\OpenAPI\Contact;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ContactListResponse'
)]
final readonly class ContactListResponseDTO
{
    public function __construct(
        #[OA\Property(
            property: 'contacts',
            description: 'Список всех контаков',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/Contact'
            )
        )]
        public array $contacts
    ) {
    }
}