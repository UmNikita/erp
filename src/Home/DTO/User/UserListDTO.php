<?php

namespace App\Home\DTO\User;

class UserListDTO
{
   public function __construct
   (
      public string $firstLetter,
      public int $id,
      public string $name,
      public string $email,
      public bool $isActive,
      public string $datetime,
      public string $department
   ) {}

}
