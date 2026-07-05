<?php

namespace App\Home\DTO\User;

class DepartmentUserCountDTO
{
   public function __construct
   (
        public int $id,
        public string $name,
        public int $userCount
   ) {}
}
