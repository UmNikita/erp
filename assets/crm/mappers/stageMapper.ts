import { Lead } from "../types/lead";
import { Stage, StageRequest, StageResponse, StageUI } from "../types/stage";

export function stageResponseToUi(stage: StageResponse, leads: Lead[] = []): StageUI {
    let amount = 0;
    leads.forEach(element => {
        amount += element.moneyAmount;
    });
    return {
        id: stage.id,
        name: stage.name,
        color: stage.color,
        leadCount: leads.length,
        moneyAmount: amount,
        leads: leads,
        sequence: stage.sequence
    }
}

export function responseToStage(stage: StageResponse): StageUI {
    return {
        id: stage.id,
        name: stage.name,
        color: stage.color,
        sequence: stage.sequence,
        leadCount: 0,
        moneyAmount: 0,
        leads: []
    }
}

export function getStageRequest(name: string, color: string, pipelineId: number): StageRequest {
    return {
      name: name,
      color: color,
      pipeline_id: pipelineId
    };
}