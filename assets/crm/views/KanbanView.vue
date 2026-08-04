<template>
    <div v-if="loading"></div>
    <div v-else>
        <div v-if="error"><KanbanError /></div>
        <div v-else>
            <Kanban v-if="pipelines.length" :pipelines="pipelines" 
            :pipelines-detail="pipelinesDetail" :set-error="setError"
            />
            <EmptyPipelines v-else @open-pipeline-modal="openModal(MODALS.CREATE_PIPELINE)" />
            <PipelineModals :pipelines-detail="pipelinesDetail" />
        </div>
    </div>
</template>

<script setup lang="ts">
    import Kanban from '../components/CRM/Kanban/Kanban/Kanban.vue';
    import EmptyPipelines from '../components/CRM/Kanban/Empty/EmptyPipelines.vue';
    import { MODALS, useModal } from '../composables/useModal.ts';
    import { usePipelines } from '../composables/CRM/pipelines/usePipelines.ts';
    import KanbanError from '../components/CRM/Kanban/Kanban/KanbanError.vue';
    import PipelineModals from '../components/CRM/Kanban/Pipeline/PipelineModals.vue';
    import { onMounted, ref } from 'vue';
    import { getPipelines, getPipelinesDetail } from '../api/pipeline.ts';

    const { openModal } = useModal();

    const loading = ref(true);
    const error = ref(false);

    onMounted(async () => {
        try{
            pipelines.value = await getPipelines();
            pipelinesDetail.value = await getPipelinesDetail();
        }
        catch {
            error.value = true;
        }
        finally {
            loading.value = false;
        }
    })
    
    const {pipelines, pipelinesDetail} = usePipelines();

    function setError() {
        error.value = true;
    }
</script>