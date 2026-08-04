<?php

namespace App\CRM\DTO\OpenAPI\LeadMessages;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'LeadMessageEditRequest'
)]
final readonly class LeadMessageEditRequestDTO
{
    public function __construct(
        #[OA\Property(
            property: 'message',
            description: 'Текст сообщения',
            type: 'string',
            example: 'Добавила в КП блок по интеграции и примеры отчётов. Проверьте, пожалуйста.'
        )]
        #[Assert\NotBlank(message: 'Message required')]
        #[Assert\Length(
            min: 1, max: 4096
        )]
        public string $message
    ) {
    }
}