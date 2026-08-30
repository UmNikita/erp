<?php

namespace App\CRM\Mapper;

use App\CRM\DTO\Client\ClientDetailDTO;
use App\CRM\DTO\Lead\LeadDetailDTO;
use App\CRM\DTO\Lead\LeadDTO;
use App\CRM\DTO\OpenAPI\Lead\LeadListResponseDTO;
use App\CRM\DTO\PaginationDTO;
use App\CRM\DTO\Pipeline\PipelineDTO;
use App\CRM\DTO\Stage\LeadStageDTO;
use App\Entity\Contact;
use App\Entity\Lead;
use App\Home\Mapper\AbstractMapper;
use App\CRM\Mapper\UserMapper;

class LeadMapper extends AbstractMapper {

    public function __construct(
        private ContactMapper $contactMapper,
        private ClientMapper $clientMapper,
        private UserMapper $userMapper,
    ) 
    {}

    public function entityToListResponse(array $values, ?PaginationDTO $pagination = null): LeadListResponseDTO {
        $leads = $this->mapList($values, function ($lead) {
            return $this->entityToDTO($lead);
        });
        return new LeadListResponseDTO($leads, $pagination);
    }

    public function entityToDetailResponse(Lead $lead): LeadDetailDTO {
        $client = $lead->getClient();
        $clientDTO = null;
        if($client != null) {
            $contacts = $client->getContacts()->toArray();
            $contactsDTO = null;
            if(count($contacts) > 0) {
                $contactsDTO = $this->mapList($contacts, function (Contact $contact) {
                    return $this->contactMapper->entityToDTO($contact);
                });
            }
            $clientDTO = $this->clientMapper->entityToDetailDTO($client, $contactsDTO);
        }
        return $this->entityToDetailDTO($lead, $clientDTO);
    }

    public function entityToDTO(Lead $lead): LeadDTO {
        if($lead->getStage()) {
            $pipeline = new PipelineDTO($lead->getStage()->getPipeline()->getId(), $lead->getStage()->getPipeline()->getName());
            $stage = new LeadStageDTO($lead->getStage()->getId(), $lead->getStage()->getName(), $pipeline);
        }
        else {
            $stage = null;
        }
        
        return new LeadDTO(
            $lead->getId(),
            $lead->getName(),
            $lead->getBudget(),
            $lead->getProduct(),
            $lead->getSource(),
            $lead->getNextAction(),
            $lead->getDateStart(),
            $lead->getDateNextAction(),
            $lead->getComment(),
            $lead->getStatus(),
            $stage,
            $lead->getResponsible() ? $this->userMapper->entityToResponsibleDTO($lead->getResponsible()) : null,
            $lead->getClient() ? $this->clientMapper->entityToDTO($lead->getClient()) : null
        );
    }

    public function entityToDetailDTO(Lead $lead, ?ClientDetailDTO $clientDTO): LeadDetailDTO {
        if($lead->getStage()) {
            $pipeline = new PipelineDTO($lead->getStage()->getPipeline()->getId(), $lead->getStage()->getPipeline()->getName());
            $stage = new LeadStageDTO($lead->getStage()->getId(), $lead->getStage()->getName(), $pipeline);
        }
        else {
            $stage = null;
        }
        return new LeadDetailDTO(
            $lead->getId(),
            $lead->getName(),
            $lead->getBudget(),
            $lead->getProduct(),
            $lead->getSource(),
            $lead->getNextAction(),
            $lead->getDateStart(),
            $lead->getDateNextAction(),
            $lead->getComment(),
            $lead->getStatus(),
            $stage,
            $lead->getResponsible() ? $this->userMapper->entityToResponsibleDTO($lead->getResponsible()) : null,
            $clientDTO
        );
    }
}