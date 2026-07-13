<?php

namespace App\CRM\RestAPIController\v1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use OpenApi\Attributes as OA;

#[Route('/crm')]
final class PipelineController extends AbstractController
{
    #[Route('/pipelines', methods: ['GET'])]
    #[OA\Get(
        summary: 'Получить список воронок',
        tags: ['CRM / Pipelines'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список воронок',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/PipelineListResponse'
                )
            )
        ]
    )]
    public function index(): Response
    {
        return $this->json(['pipeline' => 'заглушка']);
    }

    #[Route('/pipelines-detail', methods: ['GET'])]
    #[OA\Get(
        summary: 'Получить список воронок с этапами',
        tags: ['CRM / Pipelines'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список воронок с этапами',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/PipelineListDetailResponse'
                )
            )
        ]
    )]
    public function show(): Response
    {
        return $this->json(['pipeline' => 'заглушка']);
    }

    #[Route('/pipelines', methods: ['POST'])]
    #[OA\Post(
        summary: 'Создать воронку',
        tags: ['CRM / Pipelines'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/PipelineRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Воронка создана',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Pipeline'
                )
            )
        ]
    )]
    public function create(): Response
    {
        return $this->json(['pipeline' => 'заглушка'], Response::HTTP_CREATED);
    }

    #[Route('/pipeline/{id}', methods: ['PATCH'])]
    #[OA\Patch(
        summary: 'Обновить воронку',
        tags: ['CRM / Pipelines'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/PipelineRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Воронка изменена',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Pipeline'
                )
            )
        ]
    )]
    public function update(int $id): Response
    {
        return $this->json(['pipeline' => 'заглушка'], 200);
    }

    #[Route('/pipeline/{id}', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Удалить воронку',
        tags: ['CRM / Pipelines'],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Воронка удалена'
            )
            
        ]
    )]
    public function delete(int $id): Response
    {
        return $this->json(['pipeline' => 'заглушка'], 204);
    }
}
