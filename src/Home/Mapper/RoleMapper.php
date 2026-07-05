<?php

namespace App\Home\Mapper;

use App\Home\DTO\User\MatrixRoleDTO;
use App\Home\DTO\User\RoleDTO;
use App\Security\Enum\Permission;

class RoleMapper extends AbstractMapper {
    public function toDTOList(array $roles) {
        return $this->mapList($roles, function ($role) {
            return new RoleDTO(
                $role->getId(),
                $role->getName()
            );
        });
    }

    public function toMatrixDTOList(array $roles) {
        return $this->mapList($roles, function ($role) {
            $permissions = array_flip(
                $role->getPermissions()
                    ->map(fn($p) => $p->getName())
                    ->toArray()
            );

            return new MatrixRoleDTO(
                $role->getId(),
                $role->getName(),
                crm: isset($permissions[Permission::CRM_ACCESS->value]),
                repost: isset($permissions[Permission::REPOST_ACCESS->value]),
                site: isset($permissions[Permission::SITE_ACCESS->value]),
                article: isset($permissions[Permission::ARTICLE_ACCESS->value]),
                partner: isset($permissions[Permission::PARTNER_ACCESS->value]),
                statistic: isset($permissions[Permission::STATISTIC_ACCESS->value]),
                task: isset($permissions[Permission::TASK_ACCESS->value]),
                call: isset($permissions[Permission::CALL_ACCESS->value]),
                base: isset($permissions[Permission::BASE_ACCESS->value]),
                ai: isset($permissions[Permission::AI_ACCESS->value])
            );
        });
    }
}