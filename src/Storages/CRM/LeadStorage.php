<?php

namespace App\Storages\CRM;

use App\Entity\Lead;
use App\Event\CRM\LeadUpdateEvent;
use App\Repository\LeadRepository;
use App\Storages\CRMStorage;
use App\Storages\KeyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class LeadStorage extends CRMStorage
{
    public function __construct(
        private EntityManagerInterface $em,
        private LeadRepository $leadRepository,
        private Security $security,
        private EventDispatcherInterface $eventDispatcher,
        #[Autowire(service: 'crm.cache')]
        CacheInterface $cache
    ) {
        parent::__construct($cache, $em);
    }

    private Lead $receivedLead;

    public function getLeadClientsDetail(int $id): Lead
    {
        $lead = $this->leadRepository->findWithClientAndContacts($id);
        $this->hasError($lead, 'Lead');
        $this->receivedLead = clone $lead;
        return $lead;
    }

    public function getLead(int $id): Lead
    {
        $lead = $this->leadRepository->find($id);
        $this->hasError($lead, 'Lead');
        $this->receivedLead = clone $lead;
        return $lead;
    }

    public function createLead(Lead $lead)
    {
        $this->persistRecord($lead);
    }

    public function updateLead(Lead $lead)
    {
        $this->persistRecord($lead);
        $manager = $this->security->getUser();
        $event = new LeadUpdateEvent($this->receivedLead, $lead, $manager);
        $this->eventDispatcher->dispatch($event);
    }

    public function deleteLead(Lead $lead)
    {
        $this->removeRecord($lead);
    }
}
