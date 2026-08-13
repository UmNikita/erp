<?php

namespace App\CRM\Services;

use App\CRM\DTO\Client\ClientDTO;
use App\CRM\DTO\PaginationDTO;
use App\CRM\DTO\Client\EmailKPRequestDTO;
use App\CRM\DTO\OpenAPI\Client\ClientListResponseDTO;
use App\CRM\DTO\OpenAPI\Client\ClientRequestDTO;
use App\CRM\DTO\OpenAPI\Client\ClientUpdateRequestDTO;
use App\CRM\Mapper\ClientMapper;
use App\CRM\Mapper\ContactMapper;
use App\Entity\Client;
use App\Event\CRM\ClientUpdateEvent;
use App\Messages\SendKPEmailMessage;
use App\Repository\ClientRepository;
use App\Repository\ContactRepository;
use App\Shared\Pagination\PaginationFactory;
use App\Shared\Services\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ClientService {

    public function __construct(
        private EntityManagerInterface $em,
        private ClientMapper $clientMapper,
        private ClientRepository $clientRepository,
        private ContactRepository $contactRepository,
        private ContactMapper $contactMapper,
        private Security $security,
        private EventDispatcherInterface $eventDispatcher,
        private MessageBusInterface $bus
    ) 
    {}

    public function getClients(Request $request): ClientListResponseDTO {
        $search = $request->query->get('search');
        $pagination = PaginationFactory::create($request, $this->clientRepository, 10);

        if($search) {
            if ($search === '')
                $clients = [];
            else
                $clients = $this->clientRepository->search(trim($search), $pagination->offset(), $pagination->limit);
        }
        else {
            $clients = $this->clientRepository->findClients($pagination->offset(), $pagination->limit);
        }

        $paginationDTO = $pagination->getPaginationDTO($clients);
        return $this->clientMapper->entityToListResponse($clients, $paginationDTO);
    }

    public function showClient(int $id): ClientDTO {
        $client = $this->clientRepository->findWithContacts($id);
        if (!$client)
            throw new NotFoundHttpException('Client not found!');
        $metrix = $this->clientRepository->getMetricsClient($id);
        $contacts = $this->contactMapper->entityToArrayDTO($client->getContacts()->toArray());
        $clientDTO = $this->clientMapper->entityToDetailDTO($client, $contacts, $metrix);
        return $clientDTO;
    }

    public function createClient(ClientRequestDTO $request): ClientDTO {
        $client = new Client();
        $this->clientMapper->mapRequestToEntity($client, $request);
        $this->em->persist($client);
        $this->em->flush();
        return $this->clientMapper->entityToDTO($client);
    }

    public function updateClient(ClientUpdateRequestDTO $request, int $id): ClientDTO | array {
        return $this->em->wrapInTransaction(function () use ($request, $id) {
            if($request->isEmpty())
                return ["status" => "Empty body"];

            $client = $this->clientRepository->find($id);

            if (!$client)
                throw new NotFoundHttpException('Client not found!');

            $oldClient = clone $client;

            $this->clientMapper->mapRequestToEntity($client, $request);
            $this->em->persist($client);
            $this->em->flush();

            $manager = $this->security->getUser();
            $event = new ClientUpdateEvent($client, $oldClient, $manager);
            $this->eventDispatcher->dispatch($event);

            return $this->clientMapper->entityToDTO($client);
        });
    }

    public function deleteClient(int $clientID) {

        $client = $this->clientRepository->find($clientID);

        if (!$client)
            throw new NotFoundHttpException('Client not found!');

        $this->em->remove($client);
        $this->em->flush();
    }

    // public function sendEmail() {
    //     return $this->em->wrapInTransaction(function () use ($request, $id) {
            
    //         $manager = $this->security->getUser();
    //         $event = new ClientEmailSendEvent($client, $manager, '');
    //         $this->eventDispatcher->dispatch($event);

    //         return $this->clientMapper->entityToDTO($client);
    //     });
    // }

   public function sendEmailKP(int $id, EmailKPRequestDTO $emailDTO): void
    {
        $client = $this->clientRepository->find($id);

        if (!$client) {
            throw new NotFoundHttpException('Client not found!');
        }

        if (!$client->getEmail() && !$emailDTO->contact_id) {
            throw new NotFoundHttpException('Client have not email!');
        }

        if ($emailDTO->contact_id) {
            $contact = $this->contactRepository->find($emailDTO->contact_id);

            if (!$contact) {
                throw new NotFoundHttpException('Contact not found!');
            }

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