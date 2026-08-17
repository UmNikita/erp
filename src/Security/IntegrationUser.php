<?php

namespace App\Security;

use App\Entity\IntegrationToken;
use Symfony\Component\Security\Core\User\UserInterface;

final class IntegrationUser implements UserInterface
{
    public function __construct(
        private IntegrationToken $token
    ) {}

    public function getRoles(): array
    {
        return ['ROLE_API'];
    }

    public function getUserIdentifier(): string
    {
        return 'integration_' . $this->token->getId();
    }
}