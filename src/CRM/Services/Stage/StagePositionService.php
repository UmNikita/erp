<?php

namespace App\CRM\Services\Stage;

use App\Entity\Pipeline;
use App\Entity\Stage;
use App\Repository\StageRepository;
use App\Storages\CRM\StageStorage;
use Doctrine\ORM\EntityManagerInterface;

class StagePositionService {

    public function __construct(
        private StageStorage $stageStorage,
        private EntityManagerInterface $em,
        private StageRepository $stageRepository
    ) 
    {}

    public function changePositionsArray(Pipeline $pipeline, int $fromSequence, int $toSequence) {
        $stages = $this->stageRepository->findBetweenSequences($pipeline, $fromSequence, $toSequence);
        $this->changePositionStagesInArray($fromSequence, $toSequence, $stages);
        $this->stageStorage->updateStagesArray($stages);
    }
    
    public function excludeStage(Stage $stage) {
        $sequence = $stage->getSequence();
        $pipeline = $stage->getPipeline();
        $stages = $this->stageRepository->findFromSequence($pipeline, $sequence);
        foreach ($stages as $value) {
            $sequence = $value->getSequence();
            $value->setSequence($sequence - 1);
            $this->em->persist($value);
        }
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
}