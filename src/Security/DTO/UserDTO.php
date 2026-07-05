<?php

namespace App\Security\DTO;

class UserDTO
{
    public function __construct
   (
        public string $id,
        public string $name,
        public string $email,
   ) {}
}