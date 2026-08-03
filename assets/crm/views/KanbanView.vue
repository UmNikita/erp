<template>
    <div v-if="loading"></div>
    <div v-else>
        <div v-if="error"><KanbanError /></div>
        <div v-else>
            <Kanban v-if="pipelines.length" :pipelines="pipelines" :pipelines-detail="pipelinesDetail"
            @open-pipeline-edit-modal="openModal(MODALS.SETTINGS_PIPELINE)" 
            @open-pipeline-modal="openModal(MODALS.CREATE_PIPELINE)" 
            :update-pipelines-detail="updatePipelinesDetailAfterCreateStage"
            :rename-stage-pipelines-detail="renameStageForPipelinesDetail"
            :delete-stage-pipelines-detail="deleteStageForPipelinesDetail"
            :set-error="setError"
            />
            <EmptyPipelines @open-pipeline-modal="openModal(MODALS.CREATE_PIPELINE)" v-else />
            <PipelineModal
                v-if="activeModal === MODALS.CREATE_PIPELINE" :error="generalError"
                @close="closeModal" @submit="acceptAddPipeline"
            />
            <PipelineSettingsModal 
                v-if="activeModal === MODALS.SETTINGS_PIPELINE" @close="closeModal" 
                :pipelines="pipelinesDetail" @submit="acceptUpdatePipeline"
                :error="generalError"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
    import Kanban from '../components/CRM/Kanban/Kanban.vue';
    import EmptyPipelines from '../components/CRM/Kanban/Empty/EmptyPipelines.vue';
    import { PipelineModalUpdateResponeDTO, PipelineRequest } from '../types/pipeline.ts';
    import { createPipeline } from '../api/pipeline.ts';
    import PipelineModal from '../components/CRM/modals/PipelineModal.vue';
    import PipelineSettingsModal from '../components/CRM/modals/PipelineSettingsModal.vue';
    import { MODALS, useModal } from '../composables/useModal.ts';
    import { usePipelines } from '../composables/CRM/usePipelines.ts';
    import KanbanError from '../components/CRM/Kanban/KanbanError.vue';

    const {activeModal, generalError, openModal, closeModal} = useModal();
    
    const {loading, pipelines, pipelinesDetail, error, 
    updatePipelinesAfterCreate, updatePipelinesAfterUpdate, 
    getRequests, updatePipelinesDetailAfterCreateStage,
    renameStageForPipelinesDetail, deleteStageForPipelinesDetail} = usePipelines();

    function setError() {
        error.value = true;
    }

    async function acceptAddPipeline(data: PipelineRequest) {
        generalError.value = null;
        try {

            const pipeline = await createPipeline(data);
            updatePipelinesAfterCreate(pipeline);
            closeModal();

        } catch {
            generalError.value = 'Не удалось создать воронку. Попробуйте чуть позже';
        }
    }

    async function acceptUpdatePipeline(data: PipelineModalUpdateResponeDTO) {
        generalError.value = null;
        try {

            const requests = getRequests(data);
            await Promise.all(requests);
            updatePipelinesAfterUpdate(data);
            closeModal();

        } catch (error) {
            generalError.value = 'Не удалось обновить воронки. Попробуйте чуть позже';
        }
    }
</script>