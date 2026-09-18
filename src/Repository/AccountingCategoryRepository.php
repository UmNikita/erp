<?php

namespace App\Repository;

use App\Entity\AccountingCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<AccountingCategory> */
final class AccountingCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, AccountingCategory::class); }
}
