<?php

namespace App\CRM\Services;

use App\CRM\DTO\OpenAPI\Pipeline\PipelineRequestDTO;
use App\CRM\DTO\Pipeline\PipelineDTO;
use App\CRM\Mapper\PipelineMapper;
use App\Entity\Pipeline;
use App\Repository\StageRepository;
use App\Storages\CRM\PipelineStorage;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PipelineService {

    public function __construct(
        private StageRepository $stageRepository,
        private PipelineMapper $pipelineMapper,
        private PipelineStorage $pipelineStorage
    ) 
    {}

    private int $maxPipelinesCount = 10;

    public function createPipeline(PipelineRequestDTO $dto): PipelineDTO {
        $pipeline = new Pipeline();
        $countPipelines = count($this->pipelineStorage->getPipelinesWithStages());
        if($countPipelines == $this->maxPipelinesCount || $countPipelines > $this->maxPipelinesCount) {
            throw new BadRequestHttpException('Achived limit pipelines!');
        }
        $pipeline->setName($dto->name);
        $this->pipelineStorage->createOrUpdatePipeline($pipeline);
        return $this->pipelineMapper->entityToDTO($pipeline);
    }

    public function updatePipeline(int $pipelineID, PipelineRequestDTO $pipelineDTO): PipelineDTO {
        $pipeline = $this->pipelineStorage->getPipeline($pipelineID);
        $pipeline->setName($pipelineDTO->name);
        $this->pipelineStorage->createOrUpdatePipeline($pipeline);
        return $this->pipelineMapper->entityToDTO($pipeline);
    }

    public function deletePipeline(int $pipelineID) {

        $pipeline = $this->pipelineStorage->getPipeline($pipelineID);

        if ($this->stageRepository->hasByPipeline($pipeline))
            throw new BadRequestHttpException('The pipelines with the stages cannot be deleted!');

        $this->pipelineStorage->deletePipeline($pipeline);
    }
}