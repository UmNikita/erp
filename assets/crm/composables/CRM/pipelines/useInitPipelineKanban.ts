import { computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { Pipeline } from '../../../types/pipeline';
import { useKanbanStore } from '../../../stores/kanban';
import { useResponsiblesStore } from '../../../stores/responsibles';
import { usePipelineStore } from '../../../stores/pipelines';

export function useInitPipelineKanban() {
  
  const kanbanStore = useKanbanStore();
  const pipelineStore = usePipelineStore();
  const responsiblesStore = useResponsiblesStore();
  const route = useRoute();  
  const pipelineId = computed(() => {
    const queryId = Number(route.query.pipeline_id);

    if (queryId) {
      return queryId;
    }

    return pipelineStore.pipelines[0]?.id ?? null;
  });

  onMounted(async () => {
    try {
      if (pipelineId.value) {
        await responsiblesStore.loadResponsibles();
        await kanbanStore.loadKanban(pipelineId.value);
      }
    }
    catch {
      kanbanStore.error = true;
    }
  });

  watch(pipelineId, async (id) => {
    if (id) {
      await kanbanStore.loadKanban(id);
    }
  });

  return { pipelineId };
}