<?php

namespace App\CRM\Services;

use App\CRM\DTO\OpenAPI\Stage\StageRequestDTO;
use App\CRM\DTO\OpenAPI\Stage\StageRequestEditDTO;
use App\CRM\DTO\Stage\StageUIDTO;
use App\CRM\Mapper\StageMapper;
use App\Entity\Stage;
use App\Repository\LeadRepository;
use App\Repository\PipelineRepository;
use App\Repository\StageRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StageService {

    public function __construct(
        private EntityManagerInterface $em,
        private StageRepository $stageRepository,
        private PipelineRepository $pipelineRepository,
        private LeadRepository $leadRepository,
        private StageMapper $stageMapper
    ) 
    {}

    public function createStage(StageRequestDTO $stageDto): StageUIDTO
    {
        return $this->em->wrapInTransaction(function () use ($stageDto) {

            $pipeline = $this->pipelineRepository->find(
                $stageDto->pipeline_id,
                LockMode::PESSIMISTIC_WRITE
            );

            if (!$pipeline)
                throw new NotFoundHttpException('Pipeline not found!');

            $sequence = $this->stageRepository->getMaxSequenceByPipeline($pipeline) + 1;

            $stage = new Stage();

            $this->stageMapper->mapRequestDTOToEntity($stageDto, $stage, $pipeline, $sequence);

            $this->em->persist($stage);
            $this->em->flush();

            return $this->stageMapper->entityToUIDTO($stage);
        });
    }

    public function updateStage(int $id, StageRequestEditDTO $stageDto): StageUIDTO | array
    {

        if($stageDto->isEmpty())
            return ["status" => "Empty body"];

        $stage = $this->stageRepository->find($id);

        if (!$stage)
            throw new NotFoundHttpException('Stage not found!');

        $pipeline = null;
        if($stageDto->pipeline_id)
        {
            $pipeline = $this->pipelineRepository->find($stageDto->pipeline_id);
            if (!$pipeline)
                throw new NotFoundHttpException('Pipeline not found!');
        }
            
        $this->stageMapper->mapRequestDTOToEntity($stageDto, $stage, $pipeline);

        $this->em->persist($stage);
        $this->em->flush();

        return $this->stageMapper->entityToUIDTO($stage);
    }
    
    public function deleteStage(int $id)
    {

        $stage = $this->stageRepository->find($id);
        if (!$stage)
            throw new NotFoundHttpException('Stage not found!');

        if ($this->leadRepository->hasByStage($stage))
            throw new BadRequestHttpException('The stages with the leads cannot be deleted!');
        
        $this->minusSequenceAfterStages($stage);
        
        $this->em->remove($stage);
        $this->em->flush();
    }
    
    public function changePosition(int $id, int $toSequence) {
        $stage = $this->stageRepository->find($id);
        $fromSequence = $stage->getSequence();
        if($fromSequence == $toSequence) {
            return;
        }
        $pipeline = $stage->getPipeline();
        $stages = $this->stageRepository->findBetweenSequences($pipeline, $fromSequence, $toSequence);
        $this->changePositionStagesInArray($fromSequence, $toSequence, $stages);
        $this->em->flush();
    }

    private function changePositionStagesInArray(int $fromSequence, int $toSequence, array &$stages) {
        if ($fromSequence > $toSequence) {
            $startSequence = 0;
            $final = count($stages) - 1;
            foreach ($stages as $key => $stage) {
                if($key == 0) {
                    $startSequence = $stage->getSequence();
                }
                if($key == $final) {
                    $stages[$final]->setSequence($startSequence);
                    break;
                }
                $currentSequence = $stages[$key+1]->getSequence();
                $stage->setSequence($currentSequence);
            }
        } else {
            $pastSequence = 0;
            foreach ($stages as $key => $stage) {
                if($key == 0) {
                    $pastSequence = $stage->getSequence();
                    continue;
                }
                $currentSequence = $stage->getSequence();
                $stage->setSequence($pastSequence);
                $pastSequence = $currentSequence;
            }
            $stages[0]->setSequence($pastSequence);
        }
    }
    
    private function minusSequenceAfterStages(Stage $stage) {
        $sequence = $stage->getSequence();
        $pipeline = $stage->getPipeline();
        $stages = $this->stageRepository->findFromSequence($pipeline, $sequence);
        foreach ($stages as $value) {
            $sequence = $value->getSequence();
            $value->setSequence($sequence - 1);
            $this->em->persist($value);
        }
    }
}