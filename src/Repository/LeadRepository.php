<?php

namespace App\Repository;

use App\Entity\Lead;
use App\Entity\Stage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lead>
 */
class LeadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lead::class);
    }

    public function hasByStage(Stage $stage): bool
    {
        return $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.stage = :stage')
            ->setParameter('stage', $stage)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function findWithClientAndContacts(int $id): ?Lead
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.client', 'c')->addSelect('c')
            ->leftJoin('c.contacts', 'ct')->addSelect('ct')
            ->where('l.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Client[] Returns an array of Client objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Client
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
