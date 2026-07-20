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
        $allLeadsCount = 0;
        $allMoneyAmount = 0;
        $stages = $this->mapList($stages, function (Stage $stage) use (&$allLeadsCount, &$allMoneyAmount) {
            $leadsCount = 0;
            $moneyAmount = 0;
            $leads = $this->mapList($stage->getLeads()->toArray(), function (Lead $lead) use (&$leadsCount, &$moneyAmount) {
                $leadsCount++;
                $moneyAmount += $lead->getBudget();
                return $this->entityLeadToDTO($lead);
            });
            $allLeadsCount += $leadsCount;
            $allMoneyAmount += $moneyAmount;
            return $this->entityStageToDTO($stage, $leads, $leadsCount, $moneyAmount);
        });
        return $this->entityToDTO($stages, $allLeadsCount, $allMoneyAmount);
    }

    public function entityToDTO(array $stages, $leadsCount = 0, $moneyAmount = 0): KanbanDTO {
        return new KanbanDTO(
            $leadsCount,
            $moneyAmount,
            $stages
        );
    }

    public function entityStageToDTO(Stage $stage, array $leads, $leadsCount = 0, $moneyAmount = 0) {
        return new StageKanbanDTO(
            $stage->getId(),
            $stage->getName(),
            $stage->getColor(),
            $leadsCount,
            $moneyAmount,
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