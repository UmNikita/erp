<?php

namespace App\Shared\Pagination;

use App\CRM\DTO\PaginationDTO;

final readonly class Pagination
{
    public function __construct(
        public int $page,
        public int $limit,
        public int $allCount
    ) {}

    public function offset(): int
    {
        return ($this->page - 1) * $this->limit;
    }

    public function getPaginationDTO(array $records): PaginationDTO {
        return new PaginationDTO($this->page, $this->limit, count($records), $this->allCount);
    }
}