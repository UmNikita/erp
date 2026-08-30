<?php

namespace App\CRM\RestAPIController\v1;

use App\CRM\DTO\Client\EmailKPRequestDTO;
use App\CRM\DTO\OpenAPI\Client\ClientRequestDTO;
use App\CRM\DTO\OpenAPI\Client\ClientUpdateRequestDTO;
use App\CRM\Mapper\EmailMapper;
use App\CRM\RestAPIController\APIController;
use App\CRM\Services\ClientService;
use App\CRM\Services\EmailService;
use App\CRM\Services\History\JsonManager;
use App\Storages\CRM\ClientStorage;
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
        parameters: [
            new OA\Parameter(
                name: 'search',
                description: 'Найти по имени, телефону, email',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    example: 'регион Плюс'
                )
            ),
            new OA\Parameter(
                name: 'limit',
                description: 'Ограничение на записи',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'number',
                    example: 12
                )
            ),
            new OA\Parameter(
                name: 'page',
                description: 'Страница пагинации',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'number',
                    example: 2
                )
            )
        ],
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
    public function index(Request $request, ClientService $clientService): Response
    {
        $clientsDTO = $clientService->getClients($request);
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
    public function show(int $id, ClientService $clientService): Response
    {
        return $this->response($clientService->showClient($id));
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
        $errorResponse = $this->validate($clientRequest);
        if ($errorResponse)
            return $errorResponse;
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
        $errorResponse = $this->validate($clientRequest);
        if ($errorResponse)
            return $errorResponse;
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
        return $this->response(["status" => "Клиент успешно удален"], 200);
    }

    #[Route('/client/{id}/history', methods: ['GET'])]
    #[OA\Get(
        summary: 'Получить историю клиента',
        tags: ['CRM / Client'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'История лида',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'history',
                            type: 'array',
                            items: new OA\Items(
                                ref: '#/components/schemas/History'
                            )
                        )
                    ],
                    type: 'object'
                )
            )
        ]
    )]
    public function history(int $id, ClientStorage $clientStorage, JsonManager $jsonManager): Response
    {
        $client = $clientStorage->getClient($id);
        $records = $client->getClientHistoryRecords()->toArray();
        usort($records, function ($a, $b) {
            return $b->getCreatedAt() <=> $a->getCreatedAt();
        });

        $results = $jsonManager->getMessagesClient($records);
        return $this->response(["history" => $results]);
    }

    #[Route('/client/{id}/email/history', methods: ['GET'])]
    #[OA\Get(
        summary: 'Получить историю писем',
        tags: ['CRM / Client'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список клиентов',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/EmailListResponse'
                )
            )
        ]
    )]
    public function emailHistory(int $id, ClientStorage $clientStorage, EmailMapper $emailMapper): Response
    {
        $client = $clientStorage->getClient($id);
        $records = $client->getEmailLogs()->toArray();
        usort($records, function ($a, $b) {
            return $b->getCreatedAt() <=> $a->getCreatedAt();
        });
        $response = $emailMapper->entityToListResponse($records);
        
        return $this->response($response, 200);
    }

    #[Route('/client/{id}/email/kp', methods: ['POST'])]
    #[OA\Post(
        summary: 'Отправить письмом КП клиенту',
        tags: ['CRM / Client'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/EmailKPRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Письмо отправлено'
            )
            
        ]
    )]
    public function emailKp(int $id, Request $request, EmailService $emailService): Response
    {
        $emailRequest = $this->serializeRequest($request, EmailKPRequestDTO::class);
        $emailService->sendEmailKP($id, $emailRequest);
        return $this->response(["status" => "КП отправлено!"], 200);
    }
}