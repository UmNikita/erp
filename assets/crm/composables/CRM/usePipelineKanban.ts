import { computed, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { getKanban } from '../../api/kanban';
import type { Pipeline } from '../../types/pipeline';
import type { Kanban } from '../../types/kanban';
import { StageResponse } from '../../types/stage';
import { stageResponseToUi } from '../../mappers/stageMapper';
import { LeadResponse } from '../../types/lead';
import { leadResponseToUi } from '../../mappers/leadMapper';
import { mapKanban } from '../../api/lead';

export function usePipelineKanban(pipelines: Pipeline[]) {
    const route = useRoute();

    const kanban = ref<Kanban | null>(null);

    const pipelineId = computed(() => {
        const queryId = Number(route.query.pipeline_id);

        if (queryId) {
            return queryId;
        }

        return pipelines[0]?.id ?? null;
    });

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

    onMounted(() => {
        if (pipelineId.value) {
            loadKanban(pipelineId.value);
        }
    });

    watch(pipelineId, (id) => {
        if (id) {
            loadKanban(id);
        }
    });

    return {
        kanban,
        pipelineId,
        updateKanbanAfterCreateStage,
        addLeadKanban
    };
}