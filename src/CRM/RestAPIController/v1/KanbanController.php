<?php

namespace App\CRM\RestAPIController\v1;

use App\CRM\OpenAPI\Responses\KanbanResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use OpenApi\Attributes as OA;

#[Route('/crm')]
final class KanbanController extends AbstractController
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
    public function show(?int $pipelineId): Response
    {
        return $this->json(['kanban' => 'заглушка'], Response::HTTP_CREATED);
    }
}
