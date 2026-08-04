<?php

namespace App\CRM\RestAPIController\v1;

use App\CRM\DTO\OpenAPI\Stage\StageRequestDTO;
use App\CRM\DTO\OpenAPI\Stage\StageRequestEditDTO;
use App\CRM\DTO\OpenAPI\Stage\StageRequestPositionDTO;
use App\CRM\Mapper\StageMapper;
use App\CRM\RestAPIController\APIController;
use App\CRM\Services\StageService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;

#[Route('/crm')]
final class StageController extends APIController
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
                    ref: '#/components/schemas/StageUI'
                )
            )
        ]
    )]
    public function create(StageService $stageService, Request $request): Response
    {
        $dto = $this->serializeRequest($request, StageRequestDTO::class);
        $errorResponse = $this->validate($dto);
        if ($errorResponse)
            return $errorResponse;
        $stage = $stageService->createStage($dto);
        return $this->response($stage, Response::HTTP_CREATED);
    }

    #[Route('/stage/{id}', methods: ['PATCH'])]
    #[OA\Patch(
        summary: 'Обновить этап воронки',
        tags: ['CRM / Stage'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/StageRequestEdit'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Этап воронки изменен',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/StageUI'
                )
            )
        ]
    )]
    public function update(int $id, StageService $stageService, Request $request): Response
    {
        $dto = $this->serializeRequest($request, StageRequestEditDTO::class);
        $errorResponse = $this->validate($dto);
        if ($errorResponse)
            return $errorResponse;
        $stage = $stageService->updateStage($id, $dto);
        return $this->response($stage);
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
    public function delete(int $id, StageService $stageService): Response
    {
        $stageService->deleteStage($id);
        return $this->response(["status" => "Stage deleted!"], 204);
    }

    #[Route('/stage/{id}/position', methods: ['POST'])]
    #[OA\Post(
        summary: 'Изменить позицию этапа воронки',
        tags: ['CRM / Stage'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/StageRequestPosition'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Этап воронки успешно перемещен'
            )
        ]
    )]
    public function changePosition(int $id, Request $request, StageService $stageService, StageMapper $stageMapper): Response
    {
        $dto = $this->serializeRequest($request, StageRequestPositionDTO::class);
        $errorResponse = $this->validate($dto);
        if ($errorResponse)
            return $errorResponse;

        $stageService->changePosition($id, $dto->position);

        return $this->response(["status" => "Pipeline stage successfully moved"]);
    }
}