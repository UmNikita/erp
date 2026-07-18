<?php

namespace App\CRM\RestAPIController\v1;

use App\CRM\DTO\OpenAPI\LeadMessages\LeadMessagesRequestDTO;
use App\CRM\RestAPIController\APIController;
use App\CRM\Services\LeadMessageService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[Route('/crm')]
final class LeadMessagesController extends APIController
{
    #[Route('/lead/{id}/messages', methods: ['GET'])]
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
    public function show(int $id, Request $request, LeadMessageService $leadMessageService): Response
    {
        $limit = $request->query->get('limit');
        $before_id = $request->query->get('before_id');
        $res = $leadMessageService->getMessages($id, $limit, $before_id);
        return $this->response($res);
    }

    #[Route('/lead/messages', methods: ['POST'])]
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
    public function create(Request $request, LeadMessageService $leadMessageService): Response
    {
        $dto = $this->serializeRequest($request, LeadMessagesRequestDTO::class);
        $message = $leadMessageService->createMessage($dto);
        return $this->response($message, Response::HTTP_CREATED);
    }

    #[Route('/lead/message/{id}', methods: ['PATCH'])]
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
    public function update(int $id, Request $request, LeadMessageService $leadMessageService): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['message']))
            throw new BadRequestHttpException('Invalid request body');

        $message = $leadMessageService->updateMessage($id, $data['message']);
        return $this->response($message);
    }

    #[Route('/lead/message/{id}', methods: ['DELETE'])]
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
    public function delete(int $id, LeadMessageService $leadMessageService): Response
    {
        $leadMessageService->deleteMessage($id);
        return $this->response(["status" => "Воронка успешно удалена"], 204);
    }
}
