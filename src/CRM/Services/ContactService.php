<?php

namespace App\CRM\Services;

use App\CRM\DTO\Contact\ContactDTO;
use App\CRM\DTO\OpenAPI\Contact\ContactRequestDTO;
use App\CRM\DTO\OpenAPI\Contact\ContactUpdateRequestDTO;
use App\CRM\Mapper\ContactMapper;
use App\Entity\Contact;
use App\Storages\CRM\ContactStorage;
use Doctrine\ORM\EntityManagerInterface;

class ContactService {

    public function __construct(
        private EntityManagerInterface $em,
        private ContactMapper $contactMapper,
        private ContactStorage $contactStorage
    ) 
    {}

    public function createContact(ContactRequestDTO $request): ContactDTO {
        return $this->em->wrapInTransaction(function () use ($request) {
            $contact = new Contact();
            $this->contactMapper->mapRequestToEntity($contact, $request);
            $this->contactStorage->createContact($contact);
            return $this->contactMapper->entityToDTO($contact);
        });
    }

    public function updateContact(ContactUpdateRequestDTO $request, int $id): ContactDTO {
        return $this->em->wrapInTransaction(function () use ($request, $id) {
            $contact = $this->contactStorage->getContact($id);
            $this->contactMapper->mapRequestToEntity($contact, $request);
            $this->contactStorage->updateContact($contact);

            return $this->contactMapper->entityToDTO($contact);
        });
    }

    public function deleteContact(int $contactID) {
        return $this->em->wrapInTransaction(function () use ($contactID) {
            $contact = $this->contactStorage->getContact($contactID);
            $this->contactStorage->deleteContact($contact);
        });
    }

}