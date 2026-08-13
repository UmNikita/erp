<?php

namespace App\CRM\Mapper;

use App\CRM\DTO\Client\ClientDetailDTO;
use App\CRM\DTO\Lead\LeadDetailDTO;
use App\CRM\DTO\Lead\LeadDTO;
use App\CRM\DTO\OpenAPI\Lead\LeadListResponseDTO;
use App\CRM\DTO\OpenAPI\Lead\LeadRequestDTO;
use App\CRM\DTO\OpenAPI\Lead\LeadUpdateRequestDTO;
use App\CRM\DTO\Pipeline\PipelineDTO;
use App\CRM\DTO\Stage\LeadStageDTO;
use App\CRM\Enums\LeadStatus;
use App\Entity\Contact;
use App\Entity\Lead;
use App\Home\Mapper\AbstractMapper;
use App\CRM\Mapper\UserMapper;
use App\Repository\ClientRepository;
use App\Repository\StageRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LeadMapper extends AbstractMapper {

    public function __construct(
        private StageRepository $stageRepository,
        private ClientRepository $clientRepository,
        private UserRepository $userRepository,
        private ContactMapper $contactMapper,
        private ClientMapper $clientMapper,
        private UserMapper $userMapper,
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
        $pipeline = new PipelineDTO($lead->getStage()->getPipeline()->getId(), $lead->getStage()->getPipeline()->getName());
        $stage = new LeadStageDTO($lead->getStage()->getId(), $lead->getStage()->getName(), $pipeline);
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
        $pipeline = new PipelineDTO($lead->getStage()->getPipeline()->getId(), $lead->getStage()->getPipeline()->getName());
        $stage = new LeadStageDTO($lead->getStage()->getId(), $lead->getStage()->getName(), $pipeline);
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

    public function mapRequestToEntity(Lead $lead, LeadRequestDTO|LeadUpdateRequestDTO $request) {
        $name = $request->name;
        if($name)
            $lead->setName($name);

        $stage_id = $request->stage_id;
        if($stage_id) {
            $stage = $this->stageRepository->find($stage_id);
            if (!$stage)
                throw new NotFoundHttpException('Stage not found!');
            $lead->setStage($stage);
        }

        $client_id = $request->client_id;
        if($client_id) {
            $client = $this->clientRepository->find($client_id);
            if (!$client)
                throw new NotFoundHttpException('Client not found!');
            $lead->setClient($client);
        }

        $responsible_id = $request->responsible_id;
        if($responsible_id) {
            $responsible = $this->userRepository->find($responsible_id);
            if (!$responsible)
                throw new NotFoundHttpException('User not found!');
            $lead->setResponsible($responsible);
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