<?php

namespace App\CRM\Services\Stage;

use App\CRM\DTO\OpenAPI\Stage\StageRequestDTO;
use App\CRM\DTO\OpenAPI\Stage\StageRequestEditDTO;
use App\CRM\DTO\Stage\StageUIDTO;
use App\CRM\Hydrators\KanbanHydrator;
use App\CRM\Mapper\StageMapper;
use App\CRM\Services\Stage\StagePositionService;
use App\Entity\Stage;
use App\Repository\LeadRepository;
use App\Repository\StageRepository;
use App\Storages\CRM\PipelineStorage;
use App\Storages\CRM\StageStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class StageService {

    public function __construct(
        private EntityManagerInterface $em,
        private StageRepository $stageRepository,
        private LeadRepository $leadRepository,
        private StageMapper $stageMapper,
        private StageStorage $stageStorage,
        private PipelineStorage $pipelineStorage,
        private StagePositionService $stagePositionServiece,
        private KanbanHydrator $kanbanHydrator
    ) 
    {}

    public function createStage(StageRequestDTO $stageDto): StageUIDTO
    {
        return $this->em->wrapInTransaction(function () use ($stageDto) {
            
            $pipeline = $this->pipelineStorage->getPipeline($stageDto->pipeline_id, true);
            $stagesCount = count($pipeline->getStages());
            
            if($stagesCount > 20 || $stagesCount == 20) {
                throw new BadRequestHttpException('Achived limit stages!');
            }

            $sequence = $this->stageRepository->getMaxSequenceByPipeline($pipeline) + 1;

            $stage = new Stage();
            $this->kanbanHydrator->hydrateStage($stage, $stageDto);
            $stage->setPipeline($pipeline)->setSequence($sequence);

            $this->stageStorage->createOrUpdateStage($stage);

            return $this->stageMapper->entityToUIDTO($stage);
        });
    }

    public function updateStage(int $id, StageRequestEditDTO $stageDto): StageUIDTO | array
    {
        return $this->em->wrapInTransaction(function () use ($id, $stageDto) {
            if($stageDto->isEmpty())
                return ["status" => "Empty body"];

            $stage = $this->stageStorage->getStage($id);
            $this->kanbanHydrator->hydrateStage($stage, $stageDto);

            if($stageDto->pipeline_id && $stageDto->pipeline_id != $stage->getPipeline()->getId()) {
                $this->stagePositionServiece->excludeStage($stage);
                $stage->setPipeline($this->pipelineStorage->getPipeline($stageDto->pipeline_id));
                $stage->setSequence($this->stageRepository->getMaxSequence()+1);
            }
            
            $this->stageStorage->createOrUpdateStage($stage);

            return $this->stageMapper->entityToUIDTO($stage);
        });
    }
    
    public function deleteStage(int $id)
    {
        return $this->em->wrapInTransaction(function () use ($id) {
            
            $stage = $this->stageStorage->getStage($id);
            if ($this->leadRepository->hasByStage($stage) && $this->leadRepository->getCountActive($stage) > 0)
                throw new BadRequestHttpException('The stages with the leads cannot be deleted!');
            
            $this->stagePositionServiece->excludeStage($stage);
            $this->leadRepository->unbindStage($stage);
            $this->stageStorage->deleteStage($stage);
        });
    }
    
    public function changePosition(int $id, int $toSequence) {
        $stage = $this->stageRepository->find($id);
        $fromSequence = $stage->getSequence();

        if($toSequence < 1)
            throw new BadRequestHttpException('Invalud velue sequence!'); 
        
        if($fromSequence == $toSequence)
            return;

        $pipeline = $stage->getPipeline();
        
        $this->stagePositionServiece->changePositionsArray($pipeline, $fromSequence, $toSequence);
    }
}