<template>
    <div v-if="pipelineStore.loading"></div>
    <div v-else>
        <div v-if="pipelineStore.error || kanbanStore.error || responsibleStore.error"><Error /></div>
        <div v-else>
            <Kanban v-if="pipelineStore.pipelines.length" />
            <EmptyPipelines v-else @open-pipeline-modal="openModal(MODALS.CREATE_PIPELINE)" />
            <PipelineModal v-if="activeModal === MODALS.CREATE_PIPELINE" @close="closeModal" />
            <PipelineSettingsModal v-if="activeModal === MODALS.SETTINGS_PIPELINE" @close="closeModal" />
        </div>
    </div>
</template>

<script setup lang="ts">
    import Kanban from '../components/CRM/Kanban/Kanban/Kanban.vue';
    import EmptyPipelines from '../components/CRM/Kanban/Empty/EmptyPipelines.vue';
    import { MODALS, useModal } from '../composables/useModal.ts';
    import { onMounted } from 'vue';
    import Error from '../components/Error.vue';
    import { usePipelineStore } from '../stores/pipelines.ts';
    import PipelineModal from '../components/CRM/modals/PipelineModal.vue';
    import { useKanbanStore } from '../stores/kanban.ts';
    import { useResponsiblesStore } from '../stores/responsibles.ts';
    import PipelineSettingsModal from '../components/CRM/modals/PipelineSettingsModal.vue';

    const { openModal } = useModal();

    const {activeModal, closeModal} = useModal();

    const pipelineStore = usePipelineStore();
    const kanbanStore = useKanbanStore();
    const responsibleStore = useResponsiblesStore();

    onMounted(async () => {
        await pipelineStore.loadPipelines();
    })
</script>