<?php

namespace App\Home\DTO\User;

class RoleDTO
{
   public function __construct
   (
        public int $id,
        public string $name
   ) {}

}
