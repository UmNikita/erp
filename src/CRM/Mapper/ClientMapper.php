<?php

namespace App\CRM\Mapper;

use App\CRM\DTO\Client\ClientCreateLeadDTO;
use App\CRM\DTO\Client\ClientDetailDTO;
use App\CRM\DTO\Client\ClientDTO;
use App\CRM\DTO\Client\ClientMetricsDTO;
use App\CRM\DTO\OpenAPI\Client\ClientListResponseDTO;
use App\CRM\DTO\OpenAPI\Client\ClientRequestDTO;
use App\CRM\DTO\OpenAPI\Client\ClientUpdateRequestDTO;
use App\Entity\Client;
use App\Home\Mapper\AbstractMapper;

class ClientMapper extends AbstractMapper {

    public function __construct(
        private ContactMapper $contactsMapper
    )
    {}

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

    public function entityToListResponse(array $values): ClientListResponseDTO {
        $clients = $this->mapList($values, function ($client) {
            return $this->entityToDTO($client);
        });
        return new ClientListResponseDTO($clients);
    }

    public function mapMetricsToClientDetailDTO(ClientDetailDTO $client, ClientMetricsDTO $metrics) {
        $client->count_leads = $metrics->leadsCount;
        $client->ltv = $metrics->totalBudget;
        $client->amount_sum_leads = $metrics->totalBudget;
        $client->average_cheque = $metrics->averageBudget;
    }

    public function mapRequestToEntity(Client $client, ClientRequestDTO | ClientUpdateRequestDTO $request) {
        $name = $request->name;
        if($name != null)
            $client->setName($name);

        $inn = $request->inn;
        if($inn != null)
            $client->setInn($inn);

        $field_of_activity = $request->field_of_activity;
        if($field_of_activity != null)
            $client->setFieldOfActivity($field_of_activity);

        $website = $request->website;
        if($website != null)
            $client->setWebsite($website);

        $phone = $request->phone;
        if($phone != null)
            $client->setPhone($this->contactsMapper->normalizePhone($phone));

        $email = $request->email;
        if($email != null)
            $client->setEmail($this->contactsMapper->normalizeEmail($email));

        $city = $request->city;
        if($city != null)
            $client->setCity($city);

        $channel = $request->channel;
        if($channel != null)
            $client->setChannel($channel);
    }
    
    public function mapRequestLeadToEntity(Client $client, ClientCreateLeadDTO $request) {
        $name = $request->name;
        $client->setName($name);

        $phone = $request->phone;
        if($phone != null)
            $client->setPhone($this->contactsMapper->normalizePhone($phone));

        $email = $request->email;
        if($email != null)
            $client->setEmail($this->contactsMapper->normalizeEmail($email));

        $channel = $request->channel;
        if($channel != null)
            $client->setChannel($channel);
    }
}