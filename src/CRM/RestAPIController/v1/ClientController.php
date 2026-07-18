<?php

namespace App\CRM\RestAPIController\v1;

use App\CRM\DTO\OpenAPI\Client\ClientRequestDTO;
use App\CRM\DTO\OpenAPI\Client\ClientUpdateRequestDTO;
use App\CRM\Mapper\ClientMapper;
use App\CRM\Mapper\ContactMapper;
use App\CRM\RestAPIController\APIController;
use App\CRM\Services\ClientService;
use App\Repository\ClientRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;

#[Route('/crm')]
final class ClientController extends APIController
{
    #[Route('/clients', methods: ['GET'])]
    #[OA\Get(
        summary: 'Получить список клиентов',
        tags: ['CRM / Client'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список клиентов',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ClientListResponse'
                )
            )
        ]
    )]
    public function index(ClientRepository $clientRepository, ClientMapper $clientMapper): Response
    {
        $clients = $clientRepository->findAll();
        $clientsDTO = $clientMapper->entityToListResponse($clients);
        return $this->response($clientsDTO);
    }

    #[Route('/client/{id}', methods: ['GET'])]
    #[OA\Get(
        summary: 'Получить детальную информацию по клиенту',
        tags: ['CRM / Client'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Детальная информация по клиенту',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ClientDetail'
                )
            )
        ]
    )]
    public function show(int $id, ClientRepository $clientRepository, ClientMapper $clientMapper, ContactMapper $contactMapper): Response
    {
        $client = $clientRepository->findWithContacts($id);
        $contacts = $contactMapper->entityToArrayDTO($client->getContacts()->toArray());
        $clientDTO = $clientMapper->entityToDetailDTO($client, $contacts);
        return $this->response($clientDTO);
    }

    #[Route('/client', methods: ['POST'])]
    #[OA\Post(
        summary: 'Создать нового клиента',
        tags: ['CRM / Client'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/ClientRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Новый клиент создан',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Client'
                )
            )
        ]
    )]
    public function create(Request $request, ClientService $clientService): Response
    {
        $clientRequest = $this->serializeRequest($request, ClientRequestDTO::class);
        $clientResponse = $clientService->createClient($clientRequest);
        return $this->response($clientResponse, Response::HTTP_CREATED);
    }

    #[Route('/client/{id}', methods: ['PATCH'])]
    #[OA\Patch(
        summary: 'Обновить клиента',
        tags: ['CRM / Client'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/ClientRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Клиент изменен',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Client'
                )
            )
        ]
    )]
    public function update(int $id, Request $request, ClientService $clientService): Response
    {
        $clientRequest = $this->serializeRequest($request, ClientUpdateRequestDTO::class);
        $clientResponse = $clientService->updateClient($clientRequest, $id);
        return $this->response($clientResponse);
    }

    #[Route('/client/{id}', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Удалить клиента',
        tags: ['CRM / Client'],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Клиент удален'
            )
            
        ]
    )]
    public function delete(int $id, ClientService $clientService): Response
    {
        $clientService->deleteClient($id);
        return $this->response(["status" => "Клиент успешно удален"], 204);
    }
}