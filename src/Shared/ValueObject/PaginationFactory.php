<?php

namespace App\Shared\Pagination;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpFoundation\Request;

final class PaginationFactory
{
    public static function create(Request $request,  ServiceEntityRepository $repository, int $defaultLimit = 10, int $all = -1): Pagination
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = max(1, (int) $request->query->get('limit', $defaultLimit));
        if($all == -1)
            $allCount = $repository->createQueryBuilder('c')->select('COUNT(c.id)')->getQuery()->getSingleScalarResult();
        else
            $allCount = $all;

        return new Pagination($page, $limit, $allCount);
    }
}