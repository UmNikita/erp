import { updateStageLead } from '../../../api/lead';
import { useKanbanStore } from '../../../stores/kanban';

export function useMoveLead() {

    const kanbanStore = useKanbanStore();

    function moveLead(leadId: number, fromStageId: number, toStageId: number) {
        const fromStage = kanbanStore.kanban?.stages.find(stage => stage.id === fromStageId);
        const toStage = kanbanStore.kanban?.stages.find(stage => stage.id === toStageId);

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

    async function onLeadDrop(leadId: number, fromStageId: number, toStageId: number) {
        moveLead(leadId, fromStageId, toStageId);
        try {
            await updateStageLead(leadId, toStageId);
        } catch(error) {
            console.error(error);
            moveLead(leadId, toStageId, fromStageId);
        }
    }

    return { onLeadDrop };
}