<?php

namespace App\Repository;

use App\Entity\Client;
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
            ->leftJoin('l.client', 'c')
            ->addSelect('c')
            ->leftJoin('c.contacts', 'ct')
            ->addSelect('ct')
            ->leftJoin('l.responsible', 'u')
            ->addSelect('u')
            ->leftJoin('l.stage', 's')
            ->addSelect('s')
            ->leftJoin('s.pipeline', 'p')
            ->addSelect('p')
            ->where('l.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllWithClientAndResponsible(?int $clientId = null, int $offset = 0, int $limit = 12): array {
        $qb = $this->createQueryBuilder('l')
            ->leftJoin('l.client', 'c')
            ->addSelect('c')
            ->leftJoin('l.responsible', 'u')
            ->addSelect('u')
            ->leftJoin('l.stage', 's')
            ->addSelect('s')
            ->leftJoin('s.pipeline', 'p')
            ->addSelect('p')
            ->orderBy('l.date_start', 'DESC');

        if ($clientId !== null) {
            $qb->andWhere('c.id = :clientId')
            ->setParameter('clientId', $clientId);
        }

        $qb->setFirstResult($offset)
        ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function findAllArchiveResponsible(int $offset = 0, int $limit = 12): array {
        $qb = $this->createQueryBuilder('l')
            ->leftJoin('l.client', 'c')
            ->addSelect('c')
            ->leftJoin('l.responsible', 'u')
            ->addSelect('u')
            ->leftJoin('l.stage', 's')
            ->addSelect('s')
            ->leftJoin('s.pipeline', 'p')
            ->addSelect('p')
            ->andWhere('l.status != :status')
            ->setParameter('status', 'active')
            ->orderBy('l.date_start', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function findLeadsWithStagesHashTable(array $leadIds): array
    {
        return $this->createQueryBuilder('l')
            ->addSelect('s', 'p')
            ->join('l.stage', 's')
            ->join('s.pipeline', 'p')
            ->where('l.id IN (:ids)')
            ->setParameter('ids', $leadIds)
            ->getQuery()
            ->getResult();
    }

    public function getCountArchive() { 
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.status != :status')
            ->setParameter('status', 'active')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getCountActive(Stage $stage) { 
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.status = :status')
            ->andWhere('c.stage = :stage')
            ->setParameter('status', 'active')
            ->setParameter('stage', $stage)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function unbindClient(Client $client) { 
        $this->createQueryBuilder('d')
        ->update()
        ->set('d.client', ':null')
        ->where('d.client = :client')
        ->setParameter('client', $client)
        ->setParameter('null', null)
        ->getQuery()
        ->execute();
    }

    public function unbindStage(Stage $stage) { 
        $this->createQueryBuilder('d')
        ->update()
        ->set('d.stage', ':null')
        ->where('d.stage = :stage')
        ->setParameter('stage', $stage)
        ->setParameter('null', null)
        ->getQuery()
        ->execute();
    }
}
