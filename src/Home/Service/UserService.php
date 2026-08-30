<?php

namespace App\Home\Service;

use App\Entity\User;
use App\Home\Mapper\UserMapper;
use App\Storages\CRM\ResponsibleStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $em,
        private EventDispatcherInterface $dispatcher,
        private UserMapper $userMapper,
        private ResponsibleStorage $responsibleStorage
    )
    {}

    public function createUser(User $user, string $password) {

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $password
        );

        $user->setPassword($hashedPassword);

        $this->dispatcher->dispatch($this->userMapper->entityToCreatedEvent($user, $password));
        $this->responsibleStorage->createResponsible($user);
    }

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