<?php

namespace App\Repository;

use App\Entity\Pipeline;
use App\Entity\Stage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stage>
 */
class StageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stage::class);
    }

    public function hasByPipeline(Pipeline $pipeline): bool
    {
        return $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.pipeline = :pipeline')
            ->setParameter('pipeline', $pipeline)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function getMaxSequenceByPipeline(Pipeline $pipeline): int
    {
        $result = $this->createQueryBuilder('s')
            ->select('MAX(s.sequence)')
            ->where('s.pipeline = :pipeline')
            ->setParameter('pipeline', $pipeline)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ?? 0;
    }

    public function findBetweenSequences(Pipeline $pipeline, int $from, int $to): array {
        $min = min($from, $to);
        $max = max($from, $to);

        return $this->createQueryBuilder('s')
            ->where('s.pipeline = :pipeline')
            ->andWhere('s.sequence >= :min')
            ->andWhere('s.sequence <= :max')
            ->setParameter('pipeline', $pipeline)
            ->setParameter('min', $min)
            ->setParameter('max', $max)
            ->orderBy('s.sequence', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findFromSequence(Pipeline $pipeline, int $sequence): array {
        return $this->createQueryBuilder('s')
            ->where('s.pipeline = :pipeline')
            ->andWhere('s.sequence > :sequence')
            ->setParameter('pipeline', $pipeline)
            ->setParameter('sequence', $sequence)
            ->orderBy('s.sequence', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllWithLeads(int $pipelineId): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.leads', 'l')
            ->addSelect('l')
            ->leftJoin('l.client', 'c')
            ->addSelect('c')
            ->leftJoin('l.responsible', 'u')
            ->addSelect('u')
            ->where('s.pipeline = :pipelineId')
            ->setParameter('pipelineId', $pipelineId)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Stage[] Returns an array of Stage objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Stage
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
