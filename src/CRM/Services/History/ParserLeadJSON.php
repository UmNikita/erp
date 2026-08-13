<?php

namespace App\CRM\Services\History;

use App\CRM\DTO\HistoryDTO;
use App\CRM\Enums\LeadStatus;
use App\CRM\Enums\TypeLeadHistory;
use App\CRM\Services\History\DTO\ChangeLead;
use App\CRM\Services\History\DTO\ChangeStageLead;
use App\Entity\Lead;
use App\Entity\LeadHistory;
use App\Entity\Stage;
use App\Repository\StageRepository;
use RuntimeException;

class ParserLeadJSON {

    public function __construct(
        private StageRepository $stageRepository
    )
    {}
    
    public function parseToJSON(Lead $lead, TypeLeadHistory $type, ?Lead $oldLead = null): array {
        switch ($type) {
            case TypeLeadHistory::CREATED: {
                return [];
            }
            case TypeLeadHistory::UPDATED: {
                return $this->parseUpdated($lead, $oldLead);
            }
            case TypeLeadHistory::FINISH: {
                return ['result' => $lead->getStatus()];
            }
            case TypeLeadHistory::STAGE_CHANGED: {
                return $this->parseStageChanged($lead, $oldLead);
            }
        }
        return [];
    }

    private function parseUpdated(Lead $lead, ?Lead $oldLead): array {
        if($oldLead == null)
            throw new RuntimeException("Parsing error! Don't have old lead!");

        $dto = new ChangeLead();
        $dto->setDTO($oldLead, $lead);
        return $dto->toArray();
    }

    private function parseStageChanged(Lead $lead, ?Lead $oldLead): array {
        if($oldLead == null)
            throw new RuntimeException("Parsing error! Don't have old lead!");
        $dto = new ChangeStageLead();
        $dto->setDTO($oldLead, $lead);
        return $dto->toArray();
    }

    /** @var LeadHistory[] $records */
    public function parseToText(array $records): array {
        $stageIds = $this->getStagesHashTable($records);

        $result = [];

        foreach ($records as $record) {
            $message = "";
            switch ($record->getType()) {
                case TypeLeadHistory::CREATED: {
                    $message = "Сделка создана!";
                    break;
                }
                case TypeLeadHistory::UPDATED: {
                    $message = "";
                    break;
                }
                case TypeLeadHistory::FINISH: {
                    $data = $record->getData();
                    if(!$data["result"])
                        throw new \RuntimeException("Invalid data!");

                    if($data["result"] == LeadStatus::LOST->value) {
                        $message = "Сделка завершена неудачно!";
                    }
                    else {
                        $message = "Сделка завершена успешно!";
                    }
                    break;
                }
                case TypeLeadHistory::STAGE_CHANGED: {
                    $data = $record->getData();
                    $stageTo = $stageIds[$data["to"]];
                    $stageFrom = $stageIds[$data["from"]];
                    $message = "Сделка перемещена с этапа «". $stageFrom->getName() ."» воронки «". $stageFrom->getPipeline()->getName() ."» на этап «". $stageTo->getName() ."» воронки «". $stageTo->getPipeline()->getName() ."»";
                    break;
                }
            }
            $result[] = new HistoryDTO($message, $record->getManager()->getName(), $record->getCreatedAt());
        }

        return $result;
    }
    
    /** @var LeadHistory[] $records */
    private function getStagesHashTable(array $records): array {
        $stageIds = [];
        foreach ($records as $record) {
            if($record->getType() == TypeLeadHistory::STAGE_CHANGED) {
                $data = $record->getData();
                if(!$data["from"] && !$data["to"])
                    throw new \RuntimeException("Invalid data!");
                $stageIds[$data["from"]] = null;
                $stageIds[$data["to"]] = null;
            }
        }
        $stages = $this->stageRepository->findStagesWithPipelinesHashTable(array_keys($stageIds));
        $stages = array_combine(
            array_map(fn (Stage $stage) => $stage->getId(), $stages),
            $stages
        );
        return $stages;
    }
}