<?php

namespace App\CRM\RestAPIController\v1;

use App\CRM\DTO\OpenAPI\Pipeline\PipelineRequestDTO;
use App\CRM\Mapper\PipelineMapper;
use App\CRM\RestAPIController\APIController;
use App\CRM\Services\PipelineService;
use App\Repository\PipelineRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/crm')]
final class PipelineController extends APIController
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
    public function index(PipelineRepository $pipelineRepository, PipelineMapper $pipelineMapper): Response
    {
        $pipelines = $pipelineRepository->findAll();
        $dto = $pipelineMapper->entityListToResponse($pipelines);
        return $this->response($dto);
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
    public function show(PipelineRepository $pipelineRepository, PipelineMapper $pipelineMapper): Response
    {
        $pipelines = $pipelineRepository->findAllWithStages();
        $dto = $pipelineMapper->entityListToDetailResponse($pipelines);
        return $this->response($dto);
    }

    #[Route('/pipeline', methods: ['POST'])]
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
    public function create(PipelineService $pipelineService, Request $request): Response
    {
        $dto = $this->serializeRequest($request, PipelineRequestDTO::class);
        $errorResponse = $this->validate($dto);
        if ($errorResponse)
            return $errorResponse;
        $pipeline = $pipelineService->createPipeline($dto);
        return $this->response($pipeline, Response::HTTP_CREATED);
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
    public function update(int $id, Request $request, PipelineService $pipelineService): Response
    {
        $dto = $this->serializeRequest($request, PipelineRequestDTO::class);
        $errorResponse = $this->validate($dto);
        if ($errorResponse)
            return $errorResponse;
        $pipeline = $pipelineService->updatePipeline($id, $dto);
        return $this->response($pipeline);
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
    public function delete(int $id, PipelineService $pipelineService): Response
    {
        $pipelineService->deletePipeline($id);
        return $this->response(["status" => "Pipeline deleted!"], 204);
    }

}
