<?php

namespace App\Security\Service;

use App\Repository\PermissionRepository;
use App\Repository\RoleRepository;
use App\Security\Enum\Permission;
use Doctrine\ORM\EntityManagerInterface;

class RoleService
{
    public function __construct(
        private RoleRepository $roleRepository,
        private PermissionRepository $permissionRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function changePermission(int $roleId, string $permissionValue, bool $have)
    {
        switch($permissionValue) {
            case "crm":
                $permissionName = Permission::CRM_ACCESS->value;
                break;
            case "repost":
                $permissionName = Permission::REPOST_ACCESS->value;
                break;
            case "site":
                $permissionName = Permission::SITE_ACCESS->value;
                break;
            case "article":
                $permissionName = Permission::ARTICLE_ACCESS->value;
                break;
            case "partner":
                $permissionName = Permission::PARTNER_ACCESS->value;
                break;
            case "statistic":
                $permissionName = Permission::STATISTIC_ACCESS->value;
                break;
            case "task":
                $permissionName = Permission::TASK_ACCESS->value;
                break;
            case "call":
                $permissionName = Permission::CALL_ACCESS->value;
                break;
            case "base":
                $permissionName = Permission::BASE_ACCESS->value;
                break;
            case "ai":
                $permissionName = Permission::AI_ACCESS->value;
                break;
            default:
                $permissionName = Permission::CRM_ACCESS->value;
                break;
        }
        
        $role = $this->roleRepository->find($roleId);

        if (!$role) {
            throw new \RuntimeException('Role not found');
        }

        $permission = $this->permissionRepository->findOneBy([
            'name' => $permissionName
        ]);

        if (!$permission) {
            throw new \RuntimeException('Permission not found');
        }

        if ($have) {
            $role->removePermission($permission);
        } else {
            $role->addPermission($permission);
        }

        $this->entityManager->persist($role);
        $this->entityManager->flush();

        return $permission;
    }
}