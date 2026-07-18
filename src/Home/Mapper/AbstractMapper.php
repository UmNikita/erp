<?php

namespace App\Home\Mapper;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

    protected function requestKeysCheck(array $data, array $keys) {
        foreach ($keys as $key) {
            if (!isset($data[$key])) {
                throw new BadRequestHttpException('Invalid request body');
            }
        }
    }

    protected function dtoListToResponse(array $pipelines) {

    }
}