<?php
namespace App\CRM\DTO\Client;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'ClientCreateLead'
)]
class ClientCreateLeadDTO
{
    public function __construct(
        #[OA\Property(type: 'string', example: 'ООО «ТехноПарк»')]
        #[Assert\NotBlank(message: 'Name required')]
        #[Assert\Length(max: 50)]
        public string $name,

        #[OA\Property(type: 'string', example: '84951234567')]
        #[Assert\Regex(
            pattern: '/^\d+$/',
            message: 'The phone must contain only numbers.'
        )]
        public ?string $phone,

        #[OA\Property(type: 'string', example: 'info@technopark.ru')]
        #[Assert\Email(
            message: 'Invalid email'
        )]
        public ?string $email,

        #[OA\Property(type: 'string', example: 'Email')]
        #[Assert\Length(max: 50)]
        public ?string $channel

    ) {}
}