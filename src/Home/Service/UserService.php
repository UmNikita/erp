<?php

namespace App\Home\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $em
    )
    {}

    public function changePasswordUser(User $user, string $password) {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);    
        $user->setPassword($hashedPassword);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function deactivateUser(User $user) {
        $user->setIsActive(false);
        $this->em->persist($user);
        $this->em->flush();
    }
}