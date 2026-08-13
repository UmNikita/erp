<?php

namespace App\CRM\RestAPIController\v1;

use App\CRM\DTO\OpenAPI\Lead\LeadRequestDTO;
use App\CRM\DTO\OpenAPI\Lead\LeadUpdateRequestDTO;
use App\CRM\Mapper\LeadMapper;
use App\CRM\RestAPIController\APIController;
use App\CRM\Services\History\JsonManager;
use App\CRM\Services\LeadService;
use App\Repository\LeadRepository;
use App\Shared\Pagination\PaginationFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/crm')]
final class LeadController extends APIController
{

    #[Route('/leads', methods: ['GET'])]
    #[OA\Get(
        summary: 'Получить список сделок',
        tags: ['CRM / Lead'],
        parameters: [
            new OA\Parameter(
                name: 'client_id',
                description: 'Найти по клиенту',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'number',
                    example: 1
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
                description: 'Список сделок',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/LeadListResponse'
                )
            )
        ]
    )]
    public function index(Request $request, LeadRepository $leadRepository, LeadMapper $leadMapper): Response
    {
        $clientId = $request->query->get('client_id');
        $archive = $request->query->get('archive', false);
        $allCount = $leadRepository->getCountArchive();
        $pagination = PaginationFactory::create($request, $leadRepository, 12, $allCount);
        if(!$archive) {
            $leads = $leadRepository->findAllWithClientAndResponsible($clientId, $pagination->offset(), $pagination->limit);
        } else {
            $leads = $leadRepository->findAllArchiveResponsible($pagination->offset(), $pagination->limit);
        }
        $paginationDTO = $pagination->getPaginationDTO($leads);
        $response = $leadMapper->entityToListResponse($leads, $paginationDTO);
        return $this->response($response);
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
    public function show(int $id, LeadService $leadService): Response
    {
        $lead = $leadService->getDetailLead($id);
        return $this->response($lead);
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
    public function create(Request $request, LeadService $leadService): Response
    {
        $dto = $this->serializeRequest($request, LeadRequestDTO::class);
        $errorResponse = $this->validate($dto);
        if ($errorResponse)
            return $errorResponse;
        $lead = $leadService->createLead($dto);
        return $this->response($lead, Response::HTTP_CREATED);
    }

    #[Route('/lead/{id}', methods: ['PATCH'])]
    #[OA\Patch(
        summary: 'Обновить сделку',
        tags: ['CRM / Lead'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/LeadUpdateRequest'
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
    public function update(int $id, Request $request, LeadService $leadService): Response
    {
        $dto = $this->serializeRequest($request, LeadUpdateRequestDTO::class);
        $errorResponse = $this->validate($dto);
        if ($errorResponse)
            return $errorResponse;
        $lead = $leadService->updateLead($id, $dto);
        return $this->response($lead);
    }

    #[Route('/lead/{id}', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Удалить лид',
        tags: ['CRM / Lead'],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Лид удален'
            )
            
        ]
    )]
    public function delete(int $id, LeadService $leadService): Response
    {
        $leadService->deleteLead($id);
        return $this->response(["status" => "Lead deleted!"], 204);
    }

    #[Route('/lead/{id}/history', methods: ['GET'])]
    #[OA\Get(
        summary: 'Получить историю лида',
        tags: ['CRM / Lead'],
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
    public function history(int $id, LeadRepository $repository, JsonManager $jsonManager): Response
    {
        $lead = $repository->find($id);
        if(!$lead)
            throw new NotFoundHttpException('Lead not found!');

        $records = $lead->getLeadHistoryRecords()->toArray();
        $results = $jsonManager->getMessagesLead($records);
        return $this->response(["history" => $results]);
    }
}