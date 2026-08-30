<?php

namespace App\Security\Service;

use App\Entity\User;
use App\Storages\CRM\ResponsibleStorage;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationService
{

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private ResponsibleStorage $responsibleStorage
    )
    {}

    public function createRootUser(string $email, string $password) {
        $user = $this->makeUser($email, $password);
        $user->setIsRoot(true);
        $this->responsibleStorage->createResponsible($user);
    }

    private function makeUser(string $email, string $password): User {
        $user = new User();
        $user->setEmail($email);
        $hash = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hash);
        return $user;
    }
}