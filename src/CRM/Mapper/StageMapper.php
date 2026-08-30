<?php

namespace App\CRM\Mapper;

use App\CRM\DTO\Stage\StageDTO;
use App\CRM\DTO\Stage\StageUIDTO;
use App\Entity\Stage;
use App\Home\Mapper\AbstractMapper;

class StageMapper extends AbstractMapper {

    public function entityToDTO(Stage $stage) {
        return new StageDTO(
            $stage->getId(),
            $stage->getName(),
            $stage->getSequence()
        );
    }
    
    public function entityToUIDTO(Stage $stage): StageUIDTO {
        return new StageUIDTO(
            $stage->getId(),
            $stage->getName(),
            $stage->getColor(),
            $stage->getSequence(),
            $stage->getPipeline()->getId()
        );
    }
}