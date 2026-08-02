import { Ref } from 'vue';
import { Kanban } from '../../types/kanban';

export function useMoveLead(kanban: Ref<Kanban | null>) {

    function moveLead(leadId: number, fromStageId: number, toStageId: number) {
        const fromStage = kanban.value?.stages.find(stage => stage.id === fromStageId);
        const toStage = kanban.value?.stages.find(stage => stage.id === toStageId);

        if (!fromStage || !toStage) {
            return;
        }

        const index = fromStage.leads.findIndex(lead => lead.id === leadId);

        if (index === -1) {
            return;
        }

        const [lead] = fromStage.leads.splice(index, 1);
        fromStage.leadCount -= 1
        fromStage.moneyAmount -= lead.moneyAmount
        toStage.leadCount += 1
        toStage.moneyAmount += lead.moneyAmount
        toStage.leads.push(lead);
    }

    return {
        moveLead
    };
}