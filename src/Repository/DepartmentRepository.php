<?php

namespace App\Repository;

use App\Entity\Department;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Department>
 */
class DepartmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Department::class);
    }

    public function getUserCountByDepartment(): array
    {
        return $this->createQueryBuilder('d')
        ->select('d.id, d.name, COUNT(u.id) AS user_count')
        ->leftJoin(User::class, 'u', 'WITH', 'u.department = d')
        ->groupBy('d.id')
        ->addGroupBy('d.name')
        ->getQuery()
        ->getArrayResult();
    }

    //    /**
    //     * @return Department[] Returns an array of Department objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Department
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
