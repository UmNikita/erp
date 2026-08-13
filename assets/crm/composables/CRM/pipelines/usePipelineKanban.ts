import { ref } from 'vue';
import { getKanban, getResponsibles } from '../../../api/kanban';
import type { Kanban, Responsible } from '../../../types/kanban';
import { StageResponse } from '../../../types/stage';
import { stageResponseToUi } from '../../../mappers/stageMapper';
import { LeadResponse } from '../../../types/lead';
import { leadResponseToUi, mapKanban } from '../../../mappers/leadMapper';

const kanban = ref<Kanban | null>(null);
const responsibles = ref<Responsible[]>([]);

export function usePipelineKanban() {

    async function loadKanban(id: number) {
        kanban.value = mapKanban(await getKanban(id));
    }

    function updateKanbanAfterCreateStage(data: StageResponse) {
        const stage = stageResponseToUi(data, []);
        kanban.value?.stages.push(stage);
    }

    function addLeadKanban(data: LeadResponse) {
        const lead = leadResponseToUi(data);
        const stage = kanban.value?.stages.find(stage => stage.id === data.stage.id);
        if (stage != null) {
            stage.leads.push(lead);
            if(kanban.value) {
                kanban.value.leadsCount += 1;
                kanban.value.moneyAmount += lead.moneyAmount;
            }
        }
    }

    function deleteLead(leadId: number, leadStageId: number): boolean | null {
        const stage = kanban.value?.stages.find(stage => stage.id === leadStageId);
        if (!stage) {
            alert('Возникла ошибка!');
            return null;
        }
        stage.leads = stage?.leads.filter(lead => lead.id !== leadId);
        stage.leadCount--;
        return true;
    }

    async function getAllResponsibles() {
        responsibles.value = await getResponsibles();
    }

    return {
        kanban,
        getAllResponsibles,
        updateKanbanAfterCreateStage,
        addLeadKanban,
        loadKanban,
        responsibles,
        deleteLead
    };
}