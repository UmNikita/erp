<?php

namespace App\CRM\Mapper;

use App\CRM\DTO\ResponsibleDTO;
use App\Entity\User;
use App\Home\Mapper\AbstractMapper;

class UserMapper extends AbstractMapper {

    public function entityToResponsibleDTO(User $user): ResponsibleDTO {
        return new ResponsibleDTO(
            $user->getId(),
            $user->getName(),
            $user->getEmail()
        );
    }
}