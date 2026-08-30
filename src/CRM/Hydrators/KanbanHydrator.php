<?php

namespace App\CRM\Hydrators;

use App\CRM\DTO\OpenAPI\Lead\LeadRequestDTO;
use App\CRM\DTO\OpenAPI\Lead\LeadUpdateRequestDTO;
use App\CRM\DTO\OpenAPI\LeadMessages\LeadMessagesRequestDTO;
use App\CRM\DTO\OpenAPI\Stage\StageRequestDTO;
use App\CRM\DTO\OpenAPI\Stage\StageRequestEditDTO;
use App\CRM\Enums\LeadStatus;
use App\Entity\Lead;
use App\Entity\LeadMessage;
use App\Entity\Stage;
use App\Storages\CRM\ClientStorage;
use App\Storages\CRM\LeadStorage;
use App\Storages\CRM\ResponsibleStorage;
use App\Storages\CRM\StageStorage;
use Symfony\Component\Security\Core\User\UserInterface;

class KanbanHydrator {

    public function __construct(
        private StageStorage $stageStorage,
        private ClientStorage $clientStorage,
        private ResponsibleStorage $responsibleStorage,
        private LeadStorage $leadStorage
    ) 
    {}

    public function hydrateStage(Stage $stage, StageRequestDTO|StageRequestEditDTO $stageDto) {
        if($stageDto->name)
            $stage->setName($stageDto->name);

        if($stageDto->color)
            $stage->setColor($stageDto->color);
    }

    public function hydrateLead(Lead $lead, LeadRequestDTO|LeadUpdateRequestDTO $request) {
        $name = $request->name;
        if($name)
            $lead->setName($name);

        $stage_id = $request->stage_id;
        if($stage_id) {
            $stage = $this->stageStorage->getStage($stage_id);
            $lead->setStage($stage);
        }

        $client_id = $request->client_id;
        if($client_id) {
            $client = $this->clientStorage->getClient($client_id);
            $lead->setClient($client);
        }

        $responsible_id = $request->responsible_id;
        if($responsible_id) {
            $responsible = $this->responsibleStorage->getResponsible($responsible_id);
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

    public function hydrateMessageLead(LeadMessage $message, LeadMessagesRequestDTO $request, UserInterface $user) {
        $message->setMessage($request->message);
        $message->setUser($user);
        $lead = $this->leadStorage->getLead($request->lead_id);
        $message->setLead($lead);
    }
}