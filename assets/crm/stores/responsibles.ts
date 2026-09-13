import { defineStore } from 'pinia';
import { ref } from 'vue';
import { Pipeline, PipelineDetail } from '../types/pipeline';
import { getPipelines, getPipelinesDetail } from '../api/pipeline';
import { createPipeline as createPipelineApi, deletePipeline as deletePipelineApi, updatePipeline as updatePipelineApi } from "../api/pipeline";
import { pipelineToDetail } from '../mappers/pipelineMapper';
import { getResponsibles } from '../api/kanban';
import { Responsible } from '../types/kanban';

export const useResponsiblesStore = defineStore('responsibles', () => {

    const responsibles = ref<Responsible[]>([]);
    const loading = ref(false);
    const error = ref(false);

    async function loadResponsibles() {
        if (loading.value || responsibles.value.length > 0)
            return;

        loading.value = true;

        try {
            responsibles.value = await getResponsibles();
        }
        catch (e) {
            error.value = true;
            
        } 
        finally {
            loading.value = false
        }
    }

    return {
        responsibles,
        loadResponsibles,
        error
    }
})