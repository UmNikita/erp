<template>
    <StageModal
      v-if="activeModal === MODALS.CREATE_STAGE" @close="close" :errors="errors"
      @submit="acceptAddStage" :error="generalError"
    />
    <LeadModal
      v-if="activeModal === MODALS.CREATE_LEAD" :pipelinesDetail="pipelinesDetail" :errors="errors"
      @close="close" @submit="acceptAddLead" :error="generalError" :responsibles="responsibles"
    />
</template>

<script setup lang="ts">
    import { ref } from 'vue';
    import { MODALS, useModal } from '../../../../composables/useModal';
    import LeadModal from '../../modals/LeadModal.vue';
    import StageModal from '../../modals/StageModal.vue';
    import { PipelineDetail } from '../../../../types/pipeline.ts';
    import { usePipelineKanban } from '../../../../composables/CRM/pipelines/usePipelineKanban.ts';
    import { StageRequest } from '../../../../types/stage.ts';
    import { usePipelines } from '../../../../composables/CRM/pipelines/usePipelines.ts';
    import { responseToStage } from '../../../../mappers/stageMapper.ts';
    import { LeadRequest } from '../../../../types/lead.ts';
    import { useStageForm } from '../../../../composables/CRM/stages/useStageForm.ts';
    import { useLeadForm } from '../../../../composables/CRM/leads/useLeadForm.ts';

    const {activeModal, generalError, closeModal} = useModal();
    const {responsibles, updateKanbanAfterCreateStage, addLeadKanban} = usePipelineKanban();
    const { addStageToDetailPipeline } = usePipelines();
    const {createStage} = useStageForm();
    const {createLead} = useLeadForm();

    const props = defineProps<{
        pipelinesDetail: PipelineDetail[];
        pipelineId: number;
    }>();

    const errors = ref<Record<string, string>>({});
    const name = ref('');

    function close() {
        name.value = '';
        errors.value = {};
        closeModal();
    }
    
    async function acceptAddStage(data: StageRequest) {
        data.pipeline_id = props.pipelineId;
        const stage = await createStage(data, errors, generalError);
        if(stage == null) return;
        updateKanbanAfterCreateStage(stage);
        addStageToDetailPipeline(data.pipeline_id, responseToStage(stage));
        closeModal();
    }

    async function acceptAddLead(data: LeadRequest, isNew: boolean) {
        const lead = await createLead(data, isNew, errors, generalError);
        if(lead == null) return;
        addLeadKanban(lead);
        closeModal();
    }
</script>