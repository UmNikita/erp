<?php

namespace App\Repository;

use App\Entity\IntegrationToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IntegrationToken>
 */
class IntegrationTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IntegrationToken::class);
    }

    public function findActiveToken(string $token): ?IntegrationToken
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.token = :token')
            ->andWhere('t.isActive = true')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getCount(): int
    {
         return (int) $this->createQueryBuilder('i')
        ->select('COUNT(i.id)')
        ->getQuery()
        ->getSingleScalarResult();
    }
}
