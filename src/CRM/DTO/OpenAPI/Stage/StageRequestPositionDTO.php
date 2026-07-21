<?php

namespace App\CRM\DTO\OpenAPI\Stage;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'StageRequestPosition'
)]
final readonly class StageRequestPositionDTO
{
    public function __construct(
        #[OA\Property(
            property: 'position',
            type: 'integer',
            example: 6,
            description: 'Новая позиция этапа'
        )]
        #[Assert\NotBlank(message: 'Position required')]
        public ?int $position
    ) {
    }
}