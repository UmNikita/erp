<?php

namespace App\CRM\RestAPIController\v1;

use App\CRM\Mapper\KanbanMapper;
use App\CRM\RestAPIController\APIController;
use App\Repository\StageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use OpenApi\Attributes as OA;

#[Route('/crm')]
final class KanbanController extends APIController
{
    #[Route('/kanban/{pipelineId}', methods: ['GET'])]
    #[OA\Get(
        summary: 'Получить подробную Kanban-доску',
        tags: ['CRM / Kanban'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Kanban-доска',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Kanban'
                )
            )
        ]
    )]
    public function show(int $pipelineId, StageRepository $stageRepository, KanbanMapper $kanbanMapper): Response
    {
        $stages = $stageRepository->findAllWithLeads($pipelineId);
        $kanban = $kanbanMapper->entityToListResponse($stages);
        return $this->response($kanban);
    }
}
