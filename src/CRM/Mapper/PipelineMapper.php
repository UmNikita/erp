<?php

namespace App\CRM\Mapper;

use App\CRM\DTO\OpenAPI\Pipeline\PipelineListDetailResponseDTO;
use App\CRM\DTO\OpenAPI\Pipeline\PipelineListResponseDTO;
use App\CRM\DTO\Pipeline\PipelineDetailDTO;
use App\CRM\DTO\Pipeline\PipelineDTO;
use App\Entity\Pipeline;
use App\Home\Mapper\AbstractMapper;

class PipelineMapper extends AbstractMapper {

    public function __construct(
        private StageMapper $stageMapper
    )
    {}

    public function entityListToResponse(array $pipelines): PipelineListResponseDTO {
        $pipelines = $this->mapList($pipelines, function ($pipeline) {
            return $this->entityToDTO($pipeline);
        });
        return new PipelineListResponseDTO($pipelines);
    }

    public function entityListToDetailResponse(array $pipelines): PipelineListDetailResponseDTO {
        $pipelines = $this->mapList($pipelines, function ($pipeline) {
            $stagesDTO = $this->mapList($pipeline->getStages()->toArray(), function ($stage) {
                return $this->stageMapper->entityToDTO($stage);
            });
            return $this->entityToDetailDTO($pipeline, $stagesDTO);
        });
        return new PipelineListDetailResponseDTO($pipelines);
    }

    public function requestToDTO(int $id, array $data): PipelineDTO {
        $this->requestKeysCheck($data, ['name']);
        return new PipelineDTO($id, $data['name']);
    }

    public function entityToDTO(Pipeline $pipeline): PipelineDTO {
        return new PipelineDTO(
            $pipeline->getId(),
            $pipeline->getName()
        );
    }

    public function entityToDetailDTO(Pipeline $pipeline, array $stagesDTO): PipelineDetailDTO {
        return new PipelineDetailDTO(
            $pipeline->getId(),
            $pipeline->getName(),
            $stagesDTO
        );
    }
}