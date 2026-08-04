<?php

namespace App\Repository;

use App\Entity\Lead;
use App\Entity\LeadMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lead>
 */
class LeadMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LeadMessage::class);
    }

    public function findMessages(Lead $lead, int $limit = 50, ?int $beforeId = null): array
    {
        $query = $this->createQueryBuilder('message')
            ->leftJoin('message.user', 'user')
            ->addSelect('user')
            ->where('message.lead = :lead')
            ->setParameter('lead', $lead)
            ->orderBy('message.id', 'DESC')
            ->setMaxResults($limit);

        if ($beforeId)
            $query->andWhere('message.id < :beforeId')->setParameter('beforeId', $beforeId);

        return $query->getQuery()->getResult();
    }

    //    /**
    //     * @return Lead[] Returns an array of Lead objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Lead
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
