<?php

namespace App\Repository;

use App\Entity\AccountingTransaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<AccountingTransaction> */
final class AccountingTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, AccountingTransaction::class); }

    /** @return AccountingTransaction[] */
    public function findForPeriod(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): array
    {
        $query = $this->createQueryBuilder('accountingTransaction')
            ->leftJoin('accountingTransaction.allocations', 'allocation')->addSelect('allocation')
            ->leftJoin('allocation.category', 'category')->addSelect('category')
            ->leftJoin('accountingTransaction.createdBy', 'createdBy')->addSelect('createdBy')
            ->orderBy('accountingTransaction.occurredAt', 'DESC')
            ->addOrderBy('accountingTransaction.id', 'DESC');

        if ($from) $query->andWhere('accountingTransaction.occurredAt >= :from')->setParameter('from', $from);
        if ($to) $query->andWhere('accountingTransaction.occurredAt <= :to')->setParameter('to', $to);

        return $query->getQuery()->getResult();
    }
}
