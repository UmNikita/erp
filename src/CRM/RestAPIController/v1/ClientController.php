<?php

namespace App\CRM\RestAPIController\v1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use OpenApi\Attributes as OA;

#[Route('/crm')]
final class ClientController extends AbstractController
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
    public function index(): Response
    {
        return $this->json(['pipeline' => 'заглушка']);
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
    public function show(): Response
    {
        return $this->json(['pipeline' => 'заглушка']);
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
    public function create(): Response
    {
        return $this->json(['pipeline' => 'заглушка'], Response::HTTP_CREATED);
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
    public function update(int $id): Response
    {
        return $this->json(['pipeline' => 'заглушка'], 200);
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
    public function delete(int $id): Response
    {
        return $this->json(['pipeline' => 'заглушка'], 204);
    }
}