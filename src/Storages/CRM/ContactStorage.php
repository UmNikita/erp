<?php

namespace App\Storages\CRM;

use App\Entity\Contact;
use App\Event\CRM\ContactCreateEvent;
use App\Event\CRM\ContactDeleteEvent;
use App\Event\CRM\ContactUpdateEvent;
use App\Repository\ContactRepository;
use App\Storages\CRMStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ContactStorage extends CRMStorage
{
    public function __construct(
        private EntityManagerInterface $em,
        private ContactRepository $contactRepository,
        private Security $security,
        private EventDispatcherInterface $eventDispatcher,
        #[Autowire(service: 'crm.cache')]
        CacheInterface $cache
    ) {
        parent::__construct($cache, $em);
    }

    private Contact $receivedContact;

    public function getContact(int $id): Contact
    {
        $contact = $this->contactRepository->find($id);
        $this->hasError($contact, 'Contact');
        $this->receivedContact = clone $contact;
        return $contact;
    }
    
    public function createContact(Contact $contact)
    {
        $this->persistRecord($contact);
        $manager = $this->security->getUser();
        $event = new ContactCreateEvent($contact, $manager);
        $this->eventDispatcher->dispatch($event);
    }

    public function updateContact(Contact $contact)
    {
        $this->persistRecord($contact);
        $manager = $this->security->getUser();
        $event = new ContactUpdateEvent($contact, $this->receivedContact, $manager);
        $this->eventDispatcher->dispatch($event);
    }

    public function deleteContact(Contact $contact)
    {
        $manager = $this->security->getUser();
        $event = new ContactDeleteEvent($contact, $manager);
        $this->eventDispatcher->dispatch($event);
        $this->removeRecord($contact);
    }
}