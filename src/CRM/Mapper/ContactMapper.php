<?php

namespace App\CRM\Mapper;

use App\CRM\DTO\Contact\ContactDTO;
use App\CRM\DTO\OpenAPI\Contact\ContactListResponseDTO;
use App\CRM\DTO\OpenAPI\Contact\ContactRequestDTO;
use App\CRM\DTO\OpenAPI\Contact\ContactUpdateRequestDTO;
use App\Entity\Contact;
use App\Home\Mapper\AbstractMapper;
use App\Repository\ClientRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContactMapper extends AbstractMapper {

    public function __construct(
        private ClientRepository $clientRepository
    )
    {}

    public function entityToArrayDTO(array $contacts): array {
        return $this->mapList($contacts, function ($contact) {
            return $this->entityToDTO($contact);
        });
    }

    public function entityToListResponse(array $values) {
        $contacts = $this->mapList($values, function ($contact) {
            return $this->entityToDTO($contact);
        });
        return new ContactListResponseDTO($contacts);
    }

    public function entityToDTO(Contact $contact): ContactDTO {
        return new ContactDTO(
            $contact->getId(),
            $contact->getName(),
            $contact->getSecondname(),
            $contact->getThirdname(),
            $contact->getPosition(),
            $contact->getPhone(),
            $contact->getEmail(),
            $contact->getMessenger(),
            $contact->getNote(),
            $contact->getDateCreate()
        );
    }

    public function mapRequestToEntity(Contact $contact, ContactRequestDTO | ContactUpdateRequestDTO $request) {
        $name = $request->name;
        if($name != null)
            $contact->setName($name);

        $client_id = $request->client_id;
        if($client_id) {
            $client = $this->clientRepository->find($client_id);
            if (!$client)
                throw new NotFoundHttpException('Client not found!');
            $contact->setClient($client);
        }

        $secondname = $request->secondname;
        if($secondname != null)
            $contact->setSecondname($secondname);

        $thirdname = $request->thirdname;
        if($thirdname != null)
            $contact->setThirdname($thirdname);

        $position = $request->position;
        if($position != null)
            $contact->setPosition($position);

        $phone = $request->phone;
        if($phone != null)
            $contact->setPhone($phone);

        $email = $request->email;
        if($email != null)
            $contact->setEmail($email);

        $messenger = $request->messenger;
        if($messenger != null)
            $contact->setMessenger($messenger);

        $note = $request->note;
        if($note != null)
            $contact->setNote($note);
    }
}