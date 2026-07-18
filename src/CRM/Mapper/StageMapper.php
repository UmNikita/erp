<?php

namespace App\CRM\Mapper;

use App\CRM\DTO\OpenAPI\Pipeline\PipelineListDetailResponseDTO;
use App\CRM\DTO\OpenAPI\Stage\StageRequestDTO;
use App\CRM\DTO\Pipeline\PipelineDetailDTO;
use App\CRM\DTO\Stage\StageGetDTO;
use App\CRM\DTO\Stage\StageUIDTO;
use App\Entity\Pipeline;
use App\Entity\Stage;
use App\Home\Mapper\AbstractMapper;

class StageMapper extends AbstractMapper {

    public function entityToListDetailResponse(array $pipelines): PipelineListDetailResponseDTO {
        $pipelines = $this->mapList($pipelines, function ($pipeline) {
            $stagesDTO = $this->mapList($pipeline->getStages()->toArray(), function ($stage) {
                return new StageGetDTO(
                    $stage->getId(),
                    $stage->getName(),
                    $stage->getSequence()
                );
            });
            return new PipelineDetailDTO(
                $pipeline->getId(),
                $pipeline->getName(),
                $stagesDTO
            );
        });
        return new PipelineListDetailResponseDTO($pipelines);
    }

    public function entityToDTO(Stage $stage) {
        return new StageGetDTO(
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

    public function mapRequestDTOToEntity(StageRequestDTO $stageRequest, Stage $stage, ?Pipeline $pipeline = null, ?int $sequence = null) {
        if($stageRequest->name)
            $stage->setName($stageRequest->name);

        if($stageRequest->color)
            $stage->setColor($stageRequest->color);

        if($pipeline != null)
            $stage->setPipeline($pipeline);

        if($sequence != null)
            $stage->setSequence($sequence);
    }

    public function getPositionFromRequest(array $data): int {
        $this->requestKeysCheck($data, ['position']);
        return $data['position'];
    }
}