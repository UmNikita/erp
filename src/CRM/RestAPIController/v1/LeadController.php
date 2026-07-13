<?php

namespace App\CRM\RestAPIController\v1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use OpenApi\Attributes as OA;

#[Route('/crm')]
final class LeadController extends AbstractController
{

    #[Route('/leads', methods: ['GET'])]
    #[OA\Get(
        summary: 'Получить список сделок',
        tags: ['CRM / Lead'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список сделок',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/LeadListResponse'
                )
            )
        ]
    )]
    public function index(): Response
    {
        return $this->json(['pipeline' => 'заглушка']);
    }

    #[Route('/lead/{id}', methods: ['GET'])]
    #[OA\Get(
        summary: 'Получить детальную информацию по сделке',
        tags: ['CRM / Lead'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Детальная информация по сделке',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/LeadDetail'
                )
            )
        ]
    )]
    public function show(): Response
    {
        return $this->json(['pipeline' => 'заглушка']);
    }

    #[Route('/lead', methods: ['POST'])]
    #[OA\Post(
        summary: 'Создать новую сделку',
        tags: ['CRM / Lead'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/LeadRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Новая сделка создана',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Lead'
                )
            )
        ]
    )]
    public function create(): Response
    {
        return $this->json(['pipeline' => 'заглушка'], Response::HTTP_CREATED);
    }

    #[Route('/lead/{id}', methods: ['PATCH'])]
    #[OA\Patch(
        summary: 'Обновить воронку',
        tags: ['CRM / Lead'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/LeadRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Сделка изменена',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Lead'
                )
            )
        ]
    )]
    public function update(int $id): Response
    {
        return $this->json(['pipeline' => 'заглушка'], 200);
    }

    #[Route('/lead/{id}', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Удалить сделку',
        tags: ['CRM / Lead'],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Сделка удалена'
            )
            
        ]
    )]
    public function delete(int $id): Response
    {
        return $this->json(['pipeline' => 'заглушка'], 204);
    }
}