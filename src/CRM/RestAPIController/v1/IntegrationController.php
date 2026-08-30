<?php

namespace App\CRM\RestAPIController\v1;

use App\CRM\Mapper\IntegrationMapper;
use App\CRM\RestAPIController\APIController;
use App\CRM\Services\IntegrationService;
use App\Repository\IntegrationTokenRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/crm')]
final class IntegrationController extends APIController
{
    #[Route('/integrations', methods: ['GET'])]
    #[OA\Get(
        summary: 'Получить список интеграций',
        tags: ['CRM / Integration'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список интеграций',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/IntegrationListResponse'
                )
            )
        ]
    )]
    public function index(IntegrationTokenRepository $repository, IntegrationMapper $mapper): Response
    {
        $integrations = $repository->findAll();
        $reposponse = $mapper->entityToListResponse($integrations);
        return $this->response($reposponse);
    }

    #[Route('/integration', methods: ['POST'])]
    #[OA\Post(
        summary: 'Создать интеграцию',
        tags: ['CRM / Integration'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Интеграция',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Integration'
                )
            )
        ]
    )]
    public function create(IntegrationService $integrationService, IntegrationMapper $mapper): Response
    {
        $integration = $integrationService->createToken();
        $response = $mapper->entityToDTO($integration);
        return $this->response($response);
    }

    #[Route('/integration/{id}/reissue', methods: ['POST'])]
    #[OA\Post(
        summary: 'Обновить токен',
        tags: ['CRM / Integration'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Токен обновлен',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Integration'
                )
            )
        ]
    )]
    public function reissue(int $id, IntegrationService $integrationService): Response
    {
        $response = $integrationService->updateToken($id);
        return $this->response($response);
    }

    #[Route('/integration/{id}/revoke', methods: ['POST'])]
    #[OA\Post(
        summary: 'Отозвать токен',
        tags: ['CRM / Integration'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Токен отозван',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Integration'
                )
            )
        ]
    )]
    public function revoke(int $id, IntegrationService $integrationService): Response
    {
        $response = $integrationService->setActiveToken($id, false);
        return $this->response($response);
    }

    #[Route('/integration/{id}/active', methods: ['POST'])]
    #[OA\Post(
        summary: 'Активировать токен',
        tags: ['CRM / Integration'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Токен активирован',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Integration'
                )
            )
        ]
    )]
    public function active(int $id, IntegrationService $integrationService): Response
    {
        $response = $integrationService->setActiveToken($id, true);
        return $this->response($response);
    }
}