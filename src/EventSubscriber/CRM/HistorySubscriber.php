<?php

namespace App\EventSubscriber\CRM;

use App\CRM\Enums\TypeClientHistory;
use App\CRM\Enums\TypeLeadHistory;
use App\CRM\Services\History\JsonManager;
use App\Event\CRM\ClientUpdateEvent;
use App\Event\CRM\ContactCreateEvent;
use App\Event\CRM\ContactDeleteEvent;
use App\Event\CRM\ContactUpdateEvent;
use App\Event\CRM\LeadCreatedEvent;
use App\Event\CRM\LeadUpdateEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HistorySubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private JsonManager $jm
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            LeadCreatedEvent::class => 'logCreateLead',
            LeadUpdateEvent::class => 'logUpdateLead',
            ClientUpdateEvent::class => 'logUpdateClient',
            ContactCreateEvent::class => 'logCreateContact',
            ContactUpdateEvent::class => 'logUpdateContact',
            ContactDeleteEvent::class => 'logDeleteContact'
        ];
    }

    public function logCreateLead(LeadCreatedEvent $event): void
    {
        $record = $this->jm->getLeadHistory($event->getLead(), $event->getManager(), TypeLeadHistory::CREATED);
        $this->em->persist($record);
        if($event->getLead()->getClient()) {
            $record = $this->jm->getClientHistory($event->getLead()->getClient(), $event->getManager(), TypeClientHistory::LEAD_CREATED, lead: $event->getLead());
            $this->em->persist($record);
        }
    }

    public function logUpdateLead(LeadUpdateEvent $event): void
    {
        if($event->getLead()->getStage()->getId() != $event->getOldLead()->getStage()->getId()) {
            $record = $this->jm->getLeadHistory($event->getLead(), $event->getManager(), TypeLeadHistory::STAGE_CHANGED, $event->getOldLead());
        }
        else if ($event->getLead()->getStatus() != $event->getOldLead()->getStatus()) {
            $record = $this->jm->getLeadHistory($event->getLead(), $event->getManager(), TypeLeadHistory::FINISH);
            $recordUser = $this->jm->getClientHistory($event->getLead()->getClient(), $event->getManager(), TypeClientHistory::LEAD_FINISH, lead: $event->getLead());
            $this->em->persist($recordUser);
        }
        else {
            $record = $this->jm->getLeadHistory($event->getLead(), $event->getManager(), TypeLeadHistory::UPDATED, $event->getOldLead());
        }

        if($event->getLead()->getClient()) {
            if($event->getLead()->getClient()->getId() != $event->getOldLead()->getClient()->getId()) {
                $record = $this->jm->getClientHistory($event->getLead()->getClient(), $event->getManager(), TypeClientHistory::LEAD_APPOINTED, lead: $event->getLead());
                $this->em->persist($record);
            }
        }

        $this->em->persist($record);
    }

    public function logUpdateClient(ClientUpdateEvent $event): void
    {
        $record = $this->jm->getClientHistory($event->getClient(), $event->getManager(), TypeClientHistory::UPDATED, $event->getOldClient());
        $this->em->persist($record);
    }

    public function logCreateContact(ContactCreateEvent $event): void
    {
        $record = $this->jm->getClientHistory($event->getContact()->getClient(), $event->getManager(), TypeClientHistory::CONTACT_CREATED, contact: $event->getContact());
        $this->em->persist($record);
    }

    public function logUpdateContact(ContactUpdateEvent $event): void
    {
        $record = $this->jm->getClientHistory($event->getContact()->getClient(), $event->getManager(), TypeClientHistory::CONTACT_UPDATED, contact: $event->getContact(), oldContact: $event->getOldContact());
        $this->em->persist($record);
    }

    public function logDeleteContact(ContactDeleteEvent $event): void
    {
        $record = $this->jm->getClientHistory($event->getContact()->getClient(), $event->getManager(), TypeClientHistory::CONTACT_DELETE, contact: $event->getContact());
        $this->em->persist($record);
    }
}