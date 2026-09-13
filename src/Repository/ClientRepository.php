<?php

namespace App\Repository;

use App\CRM\DTO\Client\ClientMetricsDTO;
use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function getMetricsClient(int $clientId): ClientMetricsDTO {
        $data = $this->createQueryBuilder('c')
        ->select('
            COUNT(l.id) as leadsCount,
            COALESCE(SUM(l.budget), 0) as totalBudget,
            COALESCE(AVG(l.budget), 0) as averageBudget
        ')
        ->leftJoin('c.leads', 'l')
        ->where('c.id = :id')
        ->setParameter('id', $clientId)
        ->getQuery()
        ->getOneOrNullResult();
        
        return new ClientMetricsDTO($data["leadsCount"], $data["totalBudget"], $data["averageBudget"]);
    }

    public function findWithContacts(int $id): ?Client
    {
        return $this->createQueryBuilder('client')
            ->leftJoin('client.contacts', 'contact')
            ->addSelect('contact')
            ->where('client.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function search(string $query, int $offset = 0, int $limit = 10): array
    {
        return $this->createQueryBuilder('c')
            ->where('LOWER(c.name) LIKE LOWER(:query)')
            ->orWhere('LOWER(c.email) LIKE LOWER(:query)')
            ->orWhere('c.phone LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findClients(int $offset, int $limit): array
    {
        $query = $this->createQueryBuilder('c')
            ->leftJoin('c.leads', 'l')
            ->addSelect('l')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();

        return iterator_to_array(new Paginator($query));
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
