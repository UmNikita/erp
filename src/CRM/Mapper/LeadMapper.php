<?php

namespace App\CRM\Mapper;

use App\CRM\DTO\Client\ClientDetailDTO;
use App\CRM\DTO\Client\ClientMetricsDTO;
use App\CRM\DTO\Lead\LeadDetailDTO;
use App\CRM\DTO\Lead\LeadDTO;
use App\CRM\DTO\OpenAPI\Lead\LeadListResponseDTO;
use App\CRM\DTO\OpenAPI\Lead\LeadRequestDTO;
use App\CRM\DTO\OpenAPI\Lead\LeadUpdateRequestDTO;
use App\CRM\Enums\LeadStatus;
use App\Entity\Contact;
use App\Entity\Lead;
use App\Home\Mapper\AbstractMapper;
use App\Repository\StageRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LeadMapper extends AbstractMapper {

    public function __construct(
        private StageRepository $stageRepository,
        private ContactMapper $contactMapper,
        private ClientMapper $clientMapper
    ) 
    {}

    public function entityToListResponse(array $values): LeadListResponseDTO {
        $leads = $this->mapList($values, function ($lead) {
            return $this->entityToDTO($lead);
        });
        return new LeadListResponseDTO($leads);
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
            $lead->getStage()->getId()
        );
    }

    public function entityToDetailDTO(Lead $lead, ClientDetailDTO $clientDTO): LeadDetailDTO {
        return new LeadDetailDTO(
            $lead->getId(),
            $lead->getName(),
            $lead->getBudget(),
            $lead->getProduct(),
            $lead->getSource(),
            $lead->getNextAction(),
            $lead->getDateStart(),
            $lead->getNextAction(),
            $lead->getComment(),
            $lead->getStatus(),
            $lead->getStage()->getId(),
            $clientDTO
        );
    }

    public function mapRequestToEntity(Lead $lead, LeadRequestDTO|LeadUpdateRequestDTO $request) {
        $name = $request->name;
        if($name)
            $lead->setName($name);

        $stage_id = $request->stage_id;
        if($stage_id) {
            $stage = $this->stageRepository->find($stage_id);
            if (!$stage)
                throw new NotFoundHttpException('Этап не найден');
            $lead->setStage($stage);
        }
        
        $budget = $request->budget;
        if($budget)
            $lead->setBudget($budget);
        
        $product = $request->product;
        if($product)
            $lead->setProduct($product);
        
        $source = $request->source;
        if($source)
            $lead->setSource($source);
        
        $next_action = $request->next_action;
        if($next_action)
            $lead->setNextAction($next_action);
        
        $date_next_action = $request->date_next_action;
        if($date_next_action)
            $lead->setDateNextAction($date_next_action);
        
        $comment = $request->comment;
        if($comment)
            $lead->setComment($comment);
        
        if ($request instanceof LeadUpdateRequestDTO) {
            if($request->status) {
                $status = LeadStatus::from($request->status);
                $lead->setStatus($status);
            }
        }
    }
}