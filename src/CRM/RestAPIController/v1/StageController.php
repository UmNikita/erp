<?php

namespace App\CRM\RestAPIController\v1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use OpenApi\Attributes as OA;

#[Route('/crm')]
final class StageController extends AbstractController
{
    #[Route('/stage', methods: ['POST'])]
    #[OA\Post(
        summary: 'Создать этап воронки',
        tags: ['CRM / Stage'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/StageRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Этап воронки создан',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Stage'
                )
            )
        ]
    )]
    public function create(): Response
    {
        return $this->json(['pipeline' => 'заглушка'], Response::HTTP_CREATED);
    }

    #[Route('/stage/{id}', methods: ['PATCH'])]
    #[OA\Patch(
        summary: 'Обновить этап воронки',
        tags: ['CRM / Stage'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/StageRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Этап воронки изменена',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Stage'
                )
            )
        ]
    )]
    public function update(int $id): Response
    {
        return $this->json(['stage' => 'заглушка'], 200);
    }

    #[Route('/stage/{id}', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Удалить этап воронки',
        tags: ['CRM / Stage'],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Этап воронки удален'
            )
            
        ]
    )]
    public function delete(int $id): Response
    {
        return $this->json(['pipeline' => 'заглушка'], 204);
    }

    #[Route('/stage/{id}/position', methods: ['POST'])]
    #[OA\Post(
        summary: 'Изменить позицию этапа воронки',
        tags: ['CRM / Stage'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['position'],
                properties: [
                    new OA\Property(
                        property: 'position',
                        type: 'integer',
                        example: 6,
                        description: 'Новая позиция этапа'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Этап воронки успешно перемещен'
            )
        ]
    )]
    public function changePosition(int $id): Response
    {
        return $this->json(['pipeline' => 'заглушка'], Response::HTTP_CREATED);
    }
}