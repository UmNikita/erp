<?php

namespace App\Home\DTO\User;

class MatrixRoleDTO
{
   public function __construct
   (
        public int $id,
        public string $name,
        public bool $crm,
        public bool $repost,
        public bool $site,
        public bool $article,
        public bool $partner,
        public bool $statistic,
        public bool $task,
        public bool $call,
        public bool $base,
        public bool $ai
   ) {}
}
