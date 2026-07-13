<?php

namespace App\CRM\RestAPIController\v1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use OpenApi\Attributes as OA;

#[Route('/crm')]
final class ContactController extends AbstractController
{
    #[Route('/client/{id}/contacts', methods: ['GET'])]
    #[OA\Get(
        summary: 'Получить список контактов клиента',
        tags: ['CRM / Contact'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список контактов',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ContactListResponse'
                )
            )
        ]
    )]
    public function index(int $id): Response
    {
        return $this->json(['pipeline' => 'заглушка']);
    }

    #[Route('/contact', methods: ['POST'])]
    #[OA\Post(
        summary: 'Создать нового контакта',
        tags: ['CRM / Contact'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/ContactRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Новый контакт создан',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Contact'
                )
            )
        ]
    )]
    public function create(): Response
    {
        return $this->json(['pipeline' => 'заглушка'], Response::HTTP_CREATED);
    }

    #[Route('/contact/{id}', methods: ['PATCH'])]
    #[OA\Patch(
        summary: 'Обновить контакт',
        tags: ['CRM / Contact'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/ContactRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Контакт изменен',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Contact'
                )
            )
        ]
    )]
    public function update(int $id): Response
    {
        return $this->json(['pipeline' => 'заглушка'], 200);
    }

    #[Route('/contact/{id}', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Удалить контакт',
        tags: ['CRM / Contact'],
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