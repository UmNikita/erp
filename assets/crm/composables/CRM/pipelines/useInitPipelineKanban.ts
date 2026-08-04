import { computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { Pipeline } from '../../../types/pipeline';

export function useInitPipelineKanban(pipelines: Pipeline[], getAllResponsibles: any, loadKanban: any, setError: any) {
  
  const route = useRoute();  
  const pipelineId = computed(() => {
      const queryId = Number(route.query.pipeline_id);

      if (queryId) {
          return queryId;
      }

      return pipelines[0]?.id ?? null;
  });

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

  return { pipelineId };
}