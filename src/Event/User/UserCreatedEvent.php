<?php

namespace App\Event\User;

use Symfony\Contracts\EventDispatcher\Event;

final class UserCreatedEvent extends Event {
    public function __construct(
        private string $userName,
        private string $userEmail,
        private string $userPassword,
        private ?string $userDepartment,
        private ?string $userRole
    )
    {}
    public function getName() {
        return $this->userName;
    }
    public function getEmail() {
        return $this->userEmail;
    }
    public function getPassword() {
        return $this->userPassword;
    }
    public function getNameDepartment() {
        return $this->userDepartment;
    }
    public function getNameRole() {
        return $this->userRole;
    }
}