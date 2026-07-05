<?php

namespace App\Home\DTO\User;

class DepartmentDTO
{
   public function __construct
   (
        public int $id,
        public string $name
   ) {}
}
