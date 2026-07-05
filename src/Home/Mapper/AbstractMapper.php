<?php

namespace App\Home\Mapper;

abstract class AbstractMapper
{
    protected function mapList(array $items, callable $mapper): array
    {
        $result = [];

        foreach ($items as $item) {
            $result[] = $mapper($item);
        }

        return $result;
    }
}