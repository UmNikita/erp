<?php
namespace App\CRM\DTO\LeadMessages;

use App\CRM\DTO\ResponsibleDTO;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Message'
)]
class MessageDTO
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public int $id,
        
        #[OA\Property(
            type: 'string', 
            format: 'date-time', 
            example: '2026-01-01T00:00:00+00:00'
        )]
        public \DateTime $date,

        #[OA\Property(type: 'string', example: 'Добавила в КП блок по интеграции и примеры отчётов. Проверьте, пожалуйста.')]
        public string $message,
        
        #[OA\Property(
            property: 'responsible',
            ref: '#/components/schemas/Responsible'
        )]
        public ?ResponsibleDTO $responsible,

    ) {}
}