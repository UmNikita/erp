<?php
namespace App\CRM\DTO\Integration;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Integration'
)]
class IntegrationDTO
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public int $id,

        #[OA\Property(type: 'string', example: 'rh_live_3oeDDiRGLwryz0nqOP0K8K2P')]
        public string $token,

        #[OA\Property(type: 'bool', example: true)]
        public bool $is_active,

        #[OA\Property(
            type: 'string',
            format: 'date-time',
            example: '2026-02-01T00:00:00+00:00'
        )]
        public \DateTime $create_at,

        #[OA\Property(
            type: 'string',
            format: 'date-time',
            example: '2026-02-01T00:00:00+00:00'
        )]
        public \DateTime $last_used,

        #[OA\Property(type: 'integer', example: 12)]
        public int $count_requests,

    ) {}
}