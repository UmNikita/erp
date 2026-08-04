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
        const stage = kanban.value?.stages.find(stage => stage.id === data.stage_id);
        if (stage)
            stage.leads.push(lead);
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
        responsibles
    };
}