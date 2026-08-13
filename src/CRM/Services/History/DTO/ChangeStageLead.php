<?php

namespace App\CRM\Services\History\DTO;

use App\Entity\Lead;

class ChangeStageLead {

    private string $from;
    private string $to;
    
    public function setDTO(Lead $oldLead, Lead $newLead) {
        $this->from = $oldLead->getStage()->getId();
        $this->to = $newLead->getStage()->getId();
    }

    public function toArray(): array
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
        ];
    }

}