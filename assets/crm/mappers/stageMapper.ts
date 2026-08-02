import { Lead } from "../types/lead";
import { Stage, StageResponse, StageUI } from "../types/stage";

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
        leads: leads
    }
}

export function responseToStage(stage: StageResponse): Stage {
    return {
        id: stage.id,
        name: stage.name,
        sequence: stage.sequence
    }
}