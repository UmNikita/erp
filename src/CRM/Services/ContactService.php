<?php

namespace App\CRM\Services;

use App\CRM\DTO\Client\ContactDTO;
use App\CRM\DTO\OpenAPI\Contact\ContactRequestDTO;
use App\CRM\DTO\OpenAPI\Contact\ContactUpdateRequestDTO;
use App\CRM\Mapper\ContactMapper;
use App\Entity\Contact;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContactService {

    public function __construct(
        private EntityManagerInterface $em,
        private ContactMapper $contactMapper,
        private ContactRepository $contactRepository
    ) 
    {}

    public function createContact(ContactRequestDTO $request): ContactDTO {
        $contact = new Contact();
        $this->contactMapper->mapRequestToEntity($contact, $request);
        $this->em->persist($contact);
        $this->em->flush();
        return $this->contactMapper->entityToDTO($contact);
    }

    public function updateContact(ContactUpdateRequestDTO $request, int $id): ContactDTO {
        $contact = $this->contactRepository->find($id);

        if (!$contact)
            throw new NotFoundHttpException('Клиент не найден');

        $this->contactMapper->mapRequestToEntity($contact, $request);
        $this->em->persist($contact);
        $this->em->flush();
        return $this->contactMapper->entityToDTO($contact);
    }

    public function deleteContact(int $contactID) {

        $contact = $this->contactRepository->find($contactID);

        if (!$contact)
            throw new NotFoundHttpException('Клиент не найден');

        $this->em->remove($contact);
        $this->em->flush();
    }

}