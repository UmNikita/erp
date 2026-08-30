<?php

namespace App\Shared\Pagination;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpFoundation\Request;

final class PaginationFactory
{
    public static function create(Request $request,  ServiceEntityRepository $repository, int $defaultLimit = 10, int $all = -1): Pagination
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->get('limit', $defaultLimit);
        if (!ctype_digit((string) $page)) {
            throw new \InvalidArgumentException('page must be an integer');
        }
        if (!ctype_digit((string) $limit)) {
            throw new \InvalidArgumentException('limit must be an integer');
        }
        if($all == -1)
            $allCount = $repository->createQueryBuilder('c')->select('COUNT(c.id)')->getQuery()->getSingleScalarResult();
        else
            $allCount = $all;

        return new Pagination($page, $limit, $allCount);
    }
}