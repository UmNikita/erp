<?php

namespace App\CRM\RestAPIController\v1;

use App\CRM\DTO\OpenAPI\Lead\LeadRequestDTO;
use App\CRM\DTO\OpenAPI\Lead\LeadUpdateRequestDTO;
use App\CRM\Mapper\LeadMapper;
use App\CRM\RestAPIController\APIController;
use App\CRM\Services\LeadService;
use App\Repository\LeadRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;

#[Route('/crm')]
final class LeadController extends APIController
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
    public function index(LeadRepository $leadRepository, LeadMapper $leadMapper): Response
    {
        $leads = $leadMapper->entityToListResponse($leadRepository->findAll());
        return $this->response($leads);
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
        $lead = $leadService->updateLead($id, $dto);
        return $this->response($lead);
    }
}