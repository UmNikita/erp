<?php
namespace App\CRM\DTO\Client;

class ClientMetricsDTO
{
    public function __construct(
        public int $leadsCount,
        public int $totalBudget,
        public int $averageBudget,
    ) {}
}