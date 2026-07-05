<?php

namespace App\Security\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationService
{

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager
    )
    {}

    public function createRootUser(string $email, string $password) {
        $user = $this->makeUser($email, $password);
        $user->setIsRoot(true);
        $this->saveUser($user);
    }

    private function saveUser(User $user) {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    private function makeUser(string $email, string $password): User {
        $user = new User();
        $user->setEmail($email);
        $hash = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hash);
        return $user;
    }
}