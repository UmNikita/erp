<?php

namespace App\CRM\Mapper;

use App\CRM\DTO\Integration\IntegrationDTO;
use App\CRM\DTO\OpenAPI\Integration\IntegrationListResponseDTO;
use App\Entity\IntegrationToken;
use App\Home\Mapper\AbstractMapper;

class IntegrationMapper extends AbstractMapper {

    public function entityToDTO(IntegrationToken $token): IntegrationDTO {
        return new IntegrationDTO(
            $token->getId(),
            $token->getToken(),
            $token->isActive(),
            $token->getCreateAt(),
            $token->getLastUsed(),
            $token->getCountRequests()
        );
    }

    public function entityToListResponse(array $values): IntegrationListResponseDTO {
        $integrations = $this->mapList($values, function ($integration) {
            return $this->entityToDTO($integration);
        });
        return new IntegrationListResponseDTO($integrations);
    }
}