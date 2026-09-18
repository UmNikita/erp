<?php

namespace App\Repository;

use App\Entity\AccountingAllocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<AccountingAllocation> */
final class AccountingAllocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, AccountingAllocation::class); }
}
