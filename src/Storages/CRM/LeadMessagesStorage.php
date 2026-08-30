<?php

namespace App\Storages\CRM;

use App\Entity\Lead;
use App\Entity\LeadMessage;
use App\Repository\LeadMessageRepository;
use App\Storages\CRMStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;

class LeadMessagesStorage extends CRMStorage
{
    public function __construct(
        private EntityManagerInterface $em,
        private LeadMessageRepository $messageRepository,
        #[Autowire(service: 'crm.cache')]
        CacheInterface $cache
    ) {
        parent::__construct($cache, $em);
    }

    public function getLeadMessages(Lead $lead, int $limit, ?int $beforeId): array
    {
        return $this->messageRepository->findMessages($lead, $limit, $beforeId);
    }
    
    public function getMessage(int $id): LeadMessage
    {
        $message = $this->messageRepository->find($id);
        $this->hasError($message, 'Message');
        return $message;
    }

    public function createOrUpdateMessage(LeadMessage $message)
    {
        $this->persistRecord($message);
    }

    public function deleteMessage(LeadMessage $message)
    {
        $this->removeRecord($message);
    }
}
