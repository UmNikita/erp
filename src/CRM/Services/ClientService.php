<?php

namespace App\CRM\Services;

use App\CRM\DTO\Client\ClientDTO;
use App\CRM\DTO\OpenAPI\Client\ClientRequestDTO;
use App\CRM\DTO\OpenAPI\Client\ClientUpdateRequestDTO;
use App\CRM\Mapper\ClientMapper;
use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClientService {

    public function __construct(
        private EntityManagerInterface $em,
        private ClientMapper $clientMapper,
        private ClientRepository $clientRepository
    ) 
    {}

    public function createClient(ClientRequestDTO $request): ClientDTO {
        $client = new Client();
        $this->clientMapper->mapRequestToEntity($client, $request);
        $this->em->persist($client);
        $this->em->flush();
        return $this->clientMapper->entityToDTO($client);
    }

    public function updateClient(ClientUpdateRequestDTO $request, int $id): ClientDTO {
        $client = $this->clientRepository->find($id);

        if (!$client)
            throw new NotFoundHttpException('Клиент не найден');

        $this->clientMapper->mapRequestToEntity($client, $request);
        $this->em->persist($client);
        $this->em->flush();
        return $this->clientMapper->entityToDTO($client);
    }

    public function deleteClient(int $clientID) {

        $client = $this->clientRepository->find($clientID);

        if (!$client)
            throw new NotFoundHttpException('Клиент не найден');

        $this->em->remove($client);
        $this->em->flush();
    }
}