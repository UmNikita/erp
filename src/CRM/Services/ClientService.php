<?php

namespace App\CRM\Services;

use App\CRM\DTO\Client\ClientCreateLeadDTO;
use App\CRM\DTO\Client\ClientDTO;
use App\CRM\DTO\OpenAPI\Client\ClientListResponseDTO;
use App\CRM\DTO\OpenAPI\Client\ClientRequestDTO;
use App\CRM\DTO\OpenAPI\Client\ClientUpdateRequestDTO;
use App\CRM\Hydrators\ClientHydrator;
use App\CRM\Mapper\ClientMapper;
use App\CRM\Mapper\ContactMapper;
use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Shared\Pagination\PaginationFactory;
use App\Storages\CRM\ClientStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClientService {

    public function __construct(
        private EntityManagerInterface $em,
        private ClientMapper $clientMapper,
        private ClientRepository $clientRepository,
        private ContactMapper $contactMapper,
        private ClientHydrator $clientHydrator,
        private ClientStorage $clientStorage,
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
        $this->clientHydrator->hydrateClient($client, $request);
        $this->clientStorage->createClient($client);
        return $this->clientMapper->entityToDTO($client);
    }

     public function createClientLead(ClientCreateLeadDTO $clientDTO, bool $isSearch): Client {
        if($isSearch) {
            if($clientDTO->email) {
                $client = $this->clientRepository->findOneBy(['email' => $clientDTO->email]);
                if($client)
                    return $client;
            }
            if($clientDTO->phone) {
                $client = $this->clientRepository->findOneBy(['phone' => $clientDTO->phone]);
                if($client)
                    return $client;
            }
        }
        $client = new Client();
        $this->clientHydrator->hydrateRequestLead($client, $clientDTO);
        $this->clientStorage->createClient($client);
        return $client;
    }


    public function updateClient(ClientUpdateRequestDTO $request, int $id): ClientDTO | array {
        return $this->em->wrapInTransaction(function () use ($request, $id) {
            if($request->isEmpty())
                return ["status" => "Empty body"];

            $client = $this->clientStorage->getClient($id);
            $this->clientHydrator->hydrateClient($client, $request);
            $this->clientStorage->updateClient($client);

            return $this->clientMapper->entityToDTO($client);
        });
    }

    public function deleteClient(int $clientID)
    {
        $client = $this->clientStorage->getClient($clientID);
        $this->clientStorage->deleteClient($client);
    }
}