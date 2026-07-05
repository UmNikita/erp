<?php

namespace App\Home\Mapper;

use App\Home\DTO\User\DepartmentDTO;
use App\Home\DTO\User\DepartmentUserCountDTO;

class DepartmentMapper extends AbstractMapper {
    public function toDTOList(array $departments) {
        return $this->mapList($departments, function ($department) {
            return new DepartmentDTO(
                $department->getId(),
                $department->getName()
            );
        });
    }

    public function toDTOListWithUserCount(array $departments) {
        return $this->mapList($departments, function ($department) {
            return new DepartmentUserCountDTO(
                $department['id'],
                $department['name'],
                $department['user_count']
            );
        });
    }
}