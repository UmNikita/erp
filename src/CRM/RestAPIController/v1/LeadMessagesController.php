<?php

namespace App\CRM\RestAPIController\v1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use OpenApi\Attributes as OA;

#[Route('/crm')]
final class LeadMessagesController extends AbstractController
{
    #[Route('/messages', methods: ['GET'])]
    #[OA\Get(
        summary: 'Получить список сообщений сделки',
        tags: ['CRM / LeadMessages'],
        parameters: [
            new OA\Parameter(
                name: 'limit',
                description: 'Количество сообщений',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'integer',
                    default: 50,
                    example: 50
                )
            ),
            new OA\Parameter(
                name: 'before_id',
                description: 'Получить сообщения до указанного ID',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'integer',
                    example: 16
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список сообщений сделки',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/LeadMessagesListResponse'
                )
            )
        ]
    )]
    public function show(): Response
    {
        return $this->json(['pipeline' => 'заглушка']);
    }

    #[Route('/messages', methods: ['POST'])]
    #[OA\Post(
        summary: 'Создать новое сообщение',
        tags: ['CRM / LeadMessages'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/LeadMessagesRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Новое сообщение',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Message'
                )
            )
        ]
    )]
    public function create(): Response
    {
        return $this->json(['pipeline' => 'заглушка']);
    }

    #[Route('/message/{id}', methods: ['PATCH'])]
    #[OA\Patch(
        summary: 'Редактировать текущее сообщение',
        tags: ['CRM / LeadMessages'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['message'],
                properties: [
                    new OA\Property(
                        property: 'message',
                        description: 'Текст сообщения',
                        type: 'string',
                        example: 'Добавила в КП блок по интеграции и примеры отчётов. Проверьте, пожалуйста.'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Текущее сообщение',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Message'
                )
            )
        ]
    )]
    public function update(int $id): Response
    {
        return $this->json(['pipeline' => 'заглушка']);
    }

    #[Route('/message/{id}', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Удалить сообщение',
        tags: ['CRM / LeadMessages'],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Сообщение удалено'
            )
            
        ]
    )]
    public function delete(int $id): Response
    {
        return $this->json(['pipeline' => 'заглушка'], 204);
    }
}
