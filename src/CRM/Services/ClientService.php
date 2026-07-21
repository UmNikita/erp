<?php

namespace App\CRM\Services;

use App\CRM\DTO\Client\ClientDTO;
use App\CRM\DTO\OpenAPI\Client\ClientRequestDTO;
use App\CRM\DTO\OpenAPI\Client\ClientUpdateRequestDTO;
use App\CRM\Mapper\ClientMapper;
use App\CRM\Mapper\ContactMapper;
use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClientService {

    public function __construct(
        private EntityManagerInterface $em,
        private ClientMapper $clientMapper,
        private ClientRepository $clientRepository,
        private ContactMapper $contactMapper
    ) 
    {}

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
        
        if($request->isEmpty())
            return ["status" => "Empty body"];

        $client = $this->clientRepository->find($id);

        if (!$client)
            throw new NotFoundHttpException('Client not found!');

        $this->clientMapper->mapRequestToEntity($client, $request);
        $this->em->persist($client);
        $this->em->flush();
        return $this->clientMapper->entityToDTO($client);
    }

    public function deleteClient(int $clientID) {

        $client = $this->clientRepository->find($clientID);

        if (!$client)
            throw new NotFoundHttpException('Client not found!');

        $this->em->remove($client);
        $this->em->flush();
    }
}