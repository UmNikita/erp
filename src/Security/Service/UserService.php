<?php

namespace App\Security\Service;

use App\Entity\User;
use App\Security\DTO\UserDTO;
use Symfony\Bundle\SecurityBundle\Security;

class UserService
{
    public function __construct(
        private Security $security
    ) {}

    public function getCurrentUser(): ?User
    {
        return $this->security->getUser();
    }

    public function getUser(): UserDTO {
        $user = $this->getCurrentUser();
        $id = $user->getId();
        $name = $user->getName();
        $email = $user ->getEmail();
        return new UserDTO($id, $name, $email);
    }

    public function getCurrentUserNameFirstLetter(): string
    {
        $user = $this->getCurrentUser();
        return $user->getName()[0];
    }
}