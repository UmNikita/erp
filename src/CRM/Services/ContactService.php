<?php

namespace App\CRM\Services;

use App\CRM\DTO\Contact\ContactDTO;
use App\CRM\DTO\OpenAPI\Contact\ContactRequestDTO;
use App\CRM\DTO\OpenAPI\Contact\ContactUpdateRequestDTO;
use App\CRM\Mapper\ContactMapper;
use App\Entity\Contact;
use App\Event\CRM\ContactCreateEvent;
use App\Event\CRM\ContactDeleteEvent;
use App\Event\CRM\ContactUpdateEvent;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ContactService {

    public function __construct(
        private EntityManagerInterface $em,
        private ContactMapper $contactMapper,
        private ContactRepository $contactRepository,
        private Security $security,
        private EventDispatcherInterface $eventDispatcher
    ) 
    {}

    public function createContact(ContactRequestDTO $request): ContactDTO {
        return $this->em->wrapInTransaction(function () use ($request) {
            $contact = new Contact();
            $this->contactMapper->mapRequestToEntity($contact, $request);
            $this->em->persist($contact);
            $this->em->flush();

            $manager = $this->security->getUser();
            $event = new ContactCreateEvent($contact, $manager);
            $this->eventDispatcher->dispatch($event);

            return $this->contactMapper->entityToDTO($contact);
        });
    }

    public function updateContact(ContactUpdateRequestDTO $request, int $id): ContactDTO {
        return $this->em->wrapInTransaction(function () use ($request, $id) {
            $contact = $this->contactRepository->find($id);

            if (!$contact)
                throw new NotFoundHttpException('Клиент не найден');

            $oldContact = clone $contact;

            $this->contactMapper->mapRequestToEntity($contact, $request);
            $this->em->persist($contact);
            $this->em->flush();

            $manager = $this->security->getUser();
            $event = new ContactUpdateEvent($contact, $oldContact, $manager);
            $this->eventDispatcher->dispatch($event);

            return $this->contactMapper->entityToDTO($contact);
        });
    }

    public function deleteContact(int $contactID) {
        return $this->em->wrapInTransaction(function () use ($contactID) {

            $contact = $this->contactRepository->find($contactID);

            if (!$contact)
                throw new NotFoundHttpException('Клиент не найден');
            
            $manager = $this->security->getUser();
            $event = new ContactDeleteEvent($contact, $manager);
            $this->eventDispatcher->dispatch($event);

            $this->em->remove($contact);
            $this->em->flush();

        });
    }

}