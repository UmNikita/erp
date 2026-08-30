<?php

namespace App\CRM\Services;

use App\CRM\DTO\Client\EmailKPRequestDTO;
use App\Messages\SendKPEmailMessage;
use App\Storages\CRM\ClientStorage;
use App\Storages\CRM\ContactStorage;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailService {

    public function __construct(
        private Security $security,
        private MessageBusInterface $bus,
        private ClientStorage $clientStorage,
        private ContactStorage $contactStorage
    ) 
    {}

    public function sendEmailKP(int $id, EmailKPRequestDTO $emailDTO): void
    {
        $client = $this->clientStorage->getClient($id);

        if (!$client->getEmail() && !$emailDTO->contact_id) {
            throw new NotFoundHttpException('Client have not email!');
        }

        if ($emailDTO->contact_id) {
            $contact = $this->contactStorage->getContact($emailDTO->contact_id);
            if (!$contact->getEmail()) {
                throw new NotFoundHttpException('Contact have not email!');
            }
        }

        $manager = $this->security->getUser();

        $this->bus->dispatch(
            new SendKPEmailMessage(
                clientId: $client->getId(),
                contactId: $emailDTO->contact_id,
                managerId: $manager->getId(),
                managerName: $emailDTO->manager_name,
                managerPhone: $emailDTO->manager_phone,
            )
        );
    }
}