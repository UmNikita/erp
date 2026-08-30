<?php

namespace App\CRM\Mapper;

use App\CRM\DTO\Client\ClientDetailDTO;
use App\CRM\DTO\Client\ClientDTO;
use App\CRM\DTO\Client\ClientMetricsDTO;
use App\CRM\DTO\PaginationDTO;
use App\CRM\DTO\Client\ClientResponseDTO;
use App\CRM\DTO\OpenAPI\Client\ClientListResponseDTO;
use App\Entity\Client;
use App\Home\Mapper\AbstractMapper;

class ClientMapper extends AbstractMapper {

    public function entityToDetailDTO(Client $client, ?array $contactsDTO, ClientMetricsDTO $metrics = null): ClientDetailDTO {
        return new ClientDetailDTO(
            $client->getId(),
            $client->getName(),
            $client->getInn(),
            $client->getFieldOfActivity(),
            $client->getWebsite(),
            $client->getPhone(),
            $client->getEmail(),
            $client->getCity(),
            $client->getChannel(),
            $client->getDateCreate(),
            $metrics ? $metrics->totalBudget : 0,
            $metrics ? $metrics->averageBudget : 0, 
            $metrics ? $metrics->leadsCount : 0,
            $metrics ? $metrics->totalBudget : 0,
            $contactsDTO
        );
    }

    public function entityToDTO(Client $client): ClientDTO {
        return new ClientDTO(
            $client->getId(),
            $client->getName(),
            $client->getInn(),
            $client->getFieldOfActivity(),
            $client->getWebsite(),
            $client->getPhone(),
            $client->getEmail(),
            $client->getCity(),
            $client->getChannel(),
            $client->getDateCreate()
        );
    }

    public function entityToResponseDTO(Client $client): ClientDTO {
        $amountCount = 0;
        foreach ($client->getLeads() as $lead) {
            $amountCount += $lead->getBudget();
        }
        return new ClientResponseDTO(
            $client->getId(),
            $client->getName(),
            $client->getInn(),
            $client->getFieldOfActivity(),
            $client->getWebsite(),
            $client->getPhone(),
            $client->getEmail(),
            $client->getCity(),
            $client->getChannel(),
            $client->getDateCreate(),
            count($client->getLeads()),
            $amountCount
        );
    }

    public function entityToListResponse(array $values, ?PaginationDTO $pagination = null): ClientListResponseDTO {
        $clients = $this->mapList($values, function ($client) {
            return $this->entityToResponseDTO($client);
        });
        return new ClientListResponseDTO($clients, $pagination);
    }
}