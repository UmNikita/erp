<?php

namespace App\Home\PageData;

use App\Home\Mapper\DepartmentMapper;
use App\Home\Mapper\RoleMapper;
use App\Home\Mapper\UserMapper;
use App\Repository\DepartmentRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;

final class UserPageData
{
    public function __construct(
        private UserRepository $userRepository,
        private UserMapper $userMapper,
        private DepartmentRepository $departmentRepository,
        private DepartmentMapper $departmentMapper,
        private RoleRepository $roleRepository,
        private RoleMapper $roleMapper
    ) {}

    public function getViewDataIndex(): array
    {
        $rolesWithPermissions = $this->roleRepository->findAllWithPermissions();
        $roleMatrix = $this->roleMapper->toMatrixDTOList($rolesWithPermissions);

        $users = $this->userMapper->toDTOList(
            $this->userRepository->getAllWithDepartment()
        );

        $departments = $this->departmentMapper->toDTOListWithUserCount(
            $this->departmentRepository->getUserCountByDepartment()
        );

        $roles = $this->roleMapper->toDTOList(
            $this->roleRepository->findAll()
        );

        return [
            'users' => $users,
            'userCount' => count($users),
            'departments' => $departments,
            'roles' => $roles,
            'roleMatrix' => $roleMatrix
        ];
    }
}