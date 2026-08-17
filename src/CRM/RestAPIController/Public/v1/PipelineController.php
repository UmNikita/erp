<?php

namespace App\CRM\RestAPIController\Public\v1;

use App\CRM\Mapper\PipelineMapper;
use App\CRM\RestAPIController\APIController;
use App\Repository\PipelineRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PipelineController extends APIController
{

    #[Route('/pipelines', methods: ['GET'])]
    public function show(PipelineRepository $pipelineRepository, PipelineMapper $pipelineMapper): Response
    {
        $pipelines = $pipelineRepository->findAllWithStages();
        $dto = $pipelineMapper->entityListToDetailResponse($pipelines);
        return $this->response($dto);
    }
}