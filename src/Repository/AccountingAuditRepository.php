<?php

namespace App\Repository;

use App\Entity\AccountingAudit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<AccountingAudit> */
final class AccountingAuditRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, AccountingAudit::class); }

    /** @return AccountingAudit[] */
    public function findRecent(int $limit = 250): array
    {
        return $this->createQueryBuilder('audit')
            ->leftJoin('audit.user', 'user')->addSelect('user')
            ->orderBy('audit.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()->getResult();
    }
}
