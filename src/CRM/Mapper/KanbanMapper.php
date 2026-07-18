<?php

namespace App\CRM\Mapper;

use App\CRM\DTO\KanbanDTO;
use App\CRM\DTO\Lead\LeadKanbanDTO;
use App\CRM\DTO\Stage\StageKanbanDTO;
use App\Entity\Lead;
use App\Entity\Stage;
use App\Home\Mapper\AbstractMapper;

class KanbanMapper extends AbstractMapper {

    public function entityToListResponse(array $stages): KanbanDTO {
        $stages = $this->mapList($stages, function (Stage $stage) {
            $leads  = $this->mapList($stage->getLeads()->toArray(), function (Lead $lead) {
                return $this->entityLeadToDTO($lead);
            });
            return $this->entityStageToDTO($stage, $leads);
        });
        return $this->entityToDTO($stages);
    }

    public function entityToDTO(array $stages): KanbanDTO {
        return new KanbanDTO(
            0,
            0,
            $stages
        );
    }

    public function entityStageToDTO(Stage $stage, array $leads) {
        return new StageKanbanDTO(
            $stage->getId(),
            $stage->getName(),
            $stage->getColor(),
            0,0,
            $leads
        );
    }

    public function entityLeadToDTO(Lead $lead): LeadKanbanDTO {
        
        $client = null;
        $responsible = null;

        if($lead->getClient() != null)
            $client = $lead->getClient()->getName();
        if($lead->getResponsible() != null)
            $responsible = $lead->getResponsible()->getName();

        return new LeadKanbanDTO(
            $lead->getId(),
            $lead->getName(),
            $lead->getDateStart()->format('Y-m-d H:i:s'),
            $client,
            $responsible,
            $lead->getBudget()
        );
    }
}