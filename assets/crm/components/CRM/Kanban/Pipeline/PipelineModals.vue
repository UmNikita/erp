<template>
    <PipelineModal
        v-if="activeModal === MODALS.CREATE_PIPELINE" :error="generalError" :errors="errors"
        @close="close" @submit="acceptAddPipeline" v-model="name"
    />
    <PipelineSettingsModal
        v-if="activeModal === MODALS.SETTINGS_PIPELINE" @close="close" 
        :pipelines="pipelinesDetail" @submit="acceptUpdatePipeline"
        :error="generalError"
    />
</template>

<script setup lang="ts">
    import { ref } from 'vue';
    import { usePipelineForm } from '../../../../composables/CRM/pipelines/usePipelineForm.ts';
    import { MODALS, useModal } from '../../../../composables/useModal';
    import { PipelineDetail, PipelineBuffersDTO } from '../../../../types/pipeline';
    import PipelineModal from "../../modals/PipelineModal.vue";
    import PipelineSettingsModal from "../../modals/PipelineSettingsModal.vue";
    import { usePipelines } from '../../../../composables/CRM/pipelines/usePipelines.ts';

    const { addPipeline, updatePipelines } = usePipelines();
    const {activeModal, generalError, closeModal} = useModal();
    const {createPipeline, updateAndDeletePipelines} = usePipelineForm();

    const props = defineProps<{
        pipelinesDetail: PipelineDetail[];
    }>();

    const errors = ref<Record<string, string>>({});
    const name = ref('');

    async function acceptAddPipeline() {
        let pipeline = await createPipeline({name: name.value}, errors, generalError);
        if(pipeline == null)
            return;

        addPipeline(pipeline);
        closeModal();
    }

    function close() {
        name.value = '';
        errors.value = {};
        closeModal();
    }

    async function acceptUpdatePipeline(data: PipelineBuffersDTO) {
        let result = await updateAndDeletePipelines(data, errors, generalError);
        if(result == null)
            return;
        updatePipelines(data);
        closeModal();
    }
</script>