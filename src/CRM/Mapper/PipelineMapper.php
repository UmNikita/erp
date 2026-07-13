<?php

namespace App\CRM\Mapper;

use App\Home\Mapper\AbstractMapper;
use App\CRM\DTO\PipelineDTO;

class PipelineMapper extends AbstractMapper {
    public function toDTOList(array $pipelines) {
        return $this->mapList($pipelines, function ($pipeline) {
            return new PipelineDTO(
                $pipeline['id'],
                $pipeline['name']
            );
        });
    }
}