<?php

namespace App\Home\DTO\User;

class UserEditDTO
{
   public function __construct
   (
      public string $name,
      public string $email,
      public ?string $departmentId = null
   ) {}

}
