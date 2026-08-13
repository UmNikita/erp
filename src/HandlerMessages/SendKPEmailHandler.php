<?php

namespace App\HandlerMessages;

use App\CRM\Enums\EmailStatus;
use App\CRM\Enums\TypeClientHistory;
use App\CRM\Services\History\JsonManager;
use App\Entity\EmailLog;
use App\Messages\SendKPEmailMessage;
use App\Repository\ClientRepository;
use App\Repository\ContactRepository;
use App\Repository\UserRepository;
use App\Shared\Services\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendKPEmailHandler
{
    public function __construct(
        private ClientRepository $clientRepository,
        private ContactRepository $contactRepository,
        private UserRepository $userRepository,
        private EmailService $emailService,
        private EntityManagerInterface $em,
        private JsonManager $jsonManager,
    ) {}

    public function __invoke(SendKPEmailMessage $message): void
    {
        $client = $this->clientRepository->find($message->clientId);

        if (!$client) {
            throw new \RuntimeException('Client not found');
        }

        $contact = null;

        if ($message->contactId) {
            $contact = $this->contactRepository->find($message->contactId);

            if (!$contact) {
                throw new \RuntimeException('Contact not found');
            }

            $email = $contact->getEmail();
        } else {
            $email = $client->getEmail();
        }

        $result = $this->emailService->sendSyncTemplate(
            'Коммерчиское предложение',
            $email,
            'emails/reklamynet-email.twig',
            [
                'manager_name' => $message->managerName,
                'manager_phone' => $message->managerPhone,
            ]
        );

        $manager = $this->userRepository->find($message->managerId);

        if (!$manager) {
            throw new \RuntimeException('Manager not found');
        }

        $this->em->wrapInTransaction(function () use (
            $client,
            $contact,
            $manager,
            $result
        ) {
            $record = $this->jsonManager->getClientHistory(
                $client,
                $manager,
                TypeClientHistory::KP_SENDED
            );

            $this->em->persist($record);

            $emailLog = new EmailLog();
            $emailLog->setTitle('КП');
            $emailLog->setUser($manager);

            if ($contact) {
                $emailLog->setContact($contact);
                $emailLog->setClient($client);
            } else {
                $emailLog->setClient($client);
            }

            $emailLog->setStatus(
                $result
                    ? EmailStatus::SUCCESS
                    : EmailStatus::FAIL
            );

            $this->em->persist($emailLog);
        });
    }
}