<?php

namespace App\CRM\Mapper;

use App\CRM\DTO\OpenAPI\Stage\StageRequestDTO;
use App\CRM\DTO\OpenAPI\Stage\StageRequestEditDTO;
use App\CRM\DTO\OpenAPI\Stage\StageRequestPositionDTO;
use App\CRM\DTO\Stage\StageDTO;
use App\CRM\DTO\Stage\StageUIDTO;
use App\Entity\Pipeline;
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

    public function mapRequestDTOToEntity(StageRequestDTO | StageRequestEditDTO $stageRequest, Stage $stage, ?Pipeline $pipeline = null, ?int $sequence = null) {
        if($stageRequest->name)
            $stage->setName($stageRequest->name);

        if($stageRequest->color)
            $stage->setColor($stageRequest->color);

        if($pipeline != null)
            $stage->setPipeline($pipeline);

        if($sequence != null)
            $stage->setSequence($sequence);
    }
}