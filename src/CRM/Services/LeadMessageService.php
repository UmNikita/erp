<?php

namespace App\CRM\Services;

use App\CRM\DTO\LeadMessages\MessageDTO;
use App\CRM\DTO\OpenAPI\LeadMessages\LeadMessagesListResponseDTO;
use App\CRM\DTO\OpenAPI\LeadMessages\LeadMessagesRequestDTO;
use App\CRM\Hydrators\KanbanHydrator;
use App\CRM\Mapper\LeadMessageMapper;
use App\Entity\LeadMessage;
use App\Storages\CRM\LeadMessagesStorage;
use App\Storages\CRM\LeadStorage;
use Symfony\Bundle\SecurityBundle\Security;

class LeadMessageService {

    public function __construct(
        private LeadStorage $leadStorage,
        private LeadMessageMapper $messageMapper,
        private Security $security,
        private LeadMessagesStorage $messageStorage,
        private KanbanHydrator $kanbanHydrator
    ) 
    {}

    public function getMessages(int $leadId, ?int $limit, ?int $beforeId): LeadMessagesListResponseDTO
    {
        $lead = $this->leadStorage->getLead($leadId);
        if(!$limit) $limit = 25;
        $messages = $this->messageStorage->getLeadMessages($lead, $limit, $beforeId);
        return $this->messageMapper->entityListToResponse($messages, $limit, $beforeId);
    }

    public function createMessage(LeadMessagesRequestDTO $dto): MessageDTO {
        $message = new LeadMessage();
        $manager = $this->security->getUser();
        $this->kanbanHydrator->hydrateMessageLead($message, $dto, $manager);
        $this->messageStorage->createOrUpdateMessage($message);
        return $this->messageMapper->entityToDTO($message);
    }

    public function updateMessage(int $id, string $text): MessageDTO {
        $message = $this->messageStorage->getMessage($id);
        $message->setMessage($text);
        $this->messageStorage->createOrUpdateMessage($message);
        return $this->messageMapper->entityToDTO($message);
    }

    public function deleteMessage(int $messageID) {
        $message = $this->messageStorage->getMessage($messageID);
        $this->messageStorage->deleteMessage($message);
    }
}