<?php

namespace App\Home\Mapper;

use App\Entity\User;
use App\Event\User\UserCreatedEvent;
use App\Home\DTO\User\UserEditDTO;
use App\Home\DTO\User\UserListDTO;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserMapper extends AbstractMapper {
    
public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function toDTOList(array $users) {
        return $this->mapList($users, function ($user) {
            $id = $user->getId();
            $name = $user->getName();
            $firstLetter = $name[0];
            $email = $user->getEmail();
            $isActive = $user->isActive();
            $department = $user->getDepartment();
            if($department == null) {
                $departmentName = '-';
            }
            else {
                $departmentName = $department->getName();
            }
            $datetime = $user->getCreatedAt()->format('Y-m-d H:i:s');
            return new UserListDTO($firstLetter, $id, $name, $email, $isActive, $datetime, $departmentName);
        });
    }
    public function toEditDTO(User $user) {
        $name = $user->getName();
        $email = $user->getEmail();
        $department = $user->getDepartment();
        if($department != null) {
            $departmentId = $department->getId();
        }
        else {
            $departmentId = null;
        }
        return new UserEditDTO($name, $email, $departmentId);
    }

    public function formToEntity(array $dataForm): User {
        $user = new User();
        $user->setName($dataForm['name']);
        $user->setEmail($dataForm['email']);
        $user->setRole($dataForm['role'] ?? null);
        $user->setDepartment($dataForm['department'] ?? null);
        return $user;
    }

    public function entityToCreatedEvent(User $user, string $password): UserCreatedEvent {
        $department = $user->getDepartment();
        $role = $user->getRole();
        return new UserCreatedEvent(
            $user->getName(),
            $user->getEmail(),
            $password,
            $department ? $department->getName() : null,
            $role ? $role->getName() : null,
        );
    }
}