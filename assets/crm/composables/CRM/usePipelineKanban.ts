import { computed, onMounted, Ref, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { getKanban, getResponsibles } from '../../api/kanban';
import type { Pipeline } from '../../types/pipeline';
import type { Kanban, Responsible } from '../../types/kanban';
import { StageResponse } from '../../types/stage';
import { stageResponseToUi } from '../../mappers/stageMapper';
import { LeadResponse } from '../../types/lead';
import { leadResponseToUi, mapKanban } from '../../mappers/leadMapper';

export function usePipelineKanban(pipelines: Pipeline[], setError: any) {
    const route = useRoute();

    const kanban = ref<Kanban | null>(null);
    const responsibles = ref<Responsible[]>([]);
    const loading = ref(false);

    const pipelineId = computed(() => {
        const queryId = Number(route.query.pipeline_id);

        if (queryId) {
            return queryId;
        }

        return pipelines[0]?.id ?? null;
    });

    async function loadKanban(id: number) {
        kanban.value = mapKanban(await getKanban(id));
        loading.value = true;
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

    onMounted(() => {
        try {
            getAllResponsibles();
            if (pipelineId.value) {
                loadKanban(pipelineId.value);
            }
        }
        catch {
            setError();
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
        addLeadKanban,
        responsibles,
        loading
    };
}