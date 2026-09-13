<template>
    <div class="deal-top__selectors">

        <StageField
            class="deal-select__label"
            title="Этап"
            :pipelines-detail="pipelineStore.pipelinesDetail"
            v-model:selected-stage-id="selected.stage"
            v-model:selected-pipeline-id="selected.pipeline"
            :error="null"
        />

        <select
            class="deal-select"
            v-model="selected.responsible"
        >
            <option disabled :value="null">
                Выберите менеджера
            </option>

            <option
                v-for="responsible in responsiblesStore.responsibles"
                :key="responsible.id"
                :value="responsible.id"
            >
                {{ responsible.name }}
            </option>
        </select>


        <div v-if="isSelected" class="deal-btns">
            <button class="deal-btn" @click="acceptEditingList">
                Принять
            </button>

            <button class="deal-btn" @click="cancelEditingList">
                Отменить
            </button>
        </div>

    </div>
</template>

<script setup lang="ts">
    import { nextTick, reactive, ref, watch } from 'vue';
    import StageField from '../../CRM/modals/fields/StageField.vue';
    import { LeadDetail } from '../../../types/lead.ts';
    import { updateResponsibleLead, updateStageLead } from '../../../api/lead.ts';
    import { usePipelineStore } from '../../../stores/pipelines.ts';
    import { useResponsiblesStore } from '../../../stores/responsibles.ts';
    import { useKanbanStore } from '../../../stores/kanban.ts';

    const props = defineProps<{
        lead: LeadDetail;
    }>();

    const kanbanStore = useKanbanStore();
    const pipelineStore = usePipelineStore();
    const responsiblesStore = useResponsiblesStore();


    const isSelected = ref(false);
    const isCanceling = ref(false);


    const selected = reactive<{
        responsible: number | null;
        pipeline: number | null;
        stage: number | null;
    }>({
        responsible: null,
        pipeline: null,
        stage: null
    });


    const old = reactive<{
        responsible: number | null;
        pipeline: number | null;
        stage: number | null;
    }>({
        responsible: null,
        pipeline: null,
        stage: null
    });


    function initResponsible() {
        selected.responsible = props.lead.responsible?.id ?? null;
        old.responsible = selected.responsible;
    }


    function initStage() {
        const pipeline = pipelineStore.pipelinesDetail.find(pipeline =>
            pipeline.stages.some(stage => stage.id === props.lead.stage.id)
        );

        if (!pipeline)
            return;

        const stage = pipeline.stages.find(stage => stage.id === props.lead.stage.id);

        if (!stage)
            return;

        selected.pipeline = pipeline.id;
        selected.stage = stage.id;

        old.pipeline = pipeline.id;
        old.stage = stage.id;
    }


    watch(
        [
            () => props.lead,
            () => pipelineStore.pipelinesDetail,
            () => responsiblesStore.responsibles
        ],
        () => {
            initResponsible();
            initStage();
        },
        {
            immediate: true,
            deep: true
        }
    );


    watch(
        [
            () => selected.responsible,
            () => selected.pipeline,
            () => selected.stage
        ],
        () => {
            if (isCanceling.value)
                return;

            isSelected.value =
                selected.responsible !== old.responsible ||
                selected.pipeline !== old.pipeline ||
                selected.stage !== old.stage;
        }
    );


    async function saveResponsible() {
        if (selected.responsible === old.responsible)
            return;

        if (selected.responsible === null)
            return;

        await updateResponsibleLead(props.lead.id, selected.responsible);
        old.responsible = selected.responsible;
        kanbanStore.clear();
    }


    async function saveStage() {
        if (selected.stage === old.stage || selected.stage === null)
            return;

        await updateStageLead(props.lead.id, selected.stage);
        old.stage = selected.stage;
        kanbanStore.clear();
    }


    async function acceptEditingList() {
        if (!isSelected.value)
            return;

        try {
            await saveResponsible();
            await saveStage();
            old.pipeline = selected.pipeline;
            isSelected.value = false;
        }
        catch {
            alert('Возникла ошибка!');
        }
    }


    function cancelEditingList() {
        isCanceling.value = true;
        selected.responsible = old.responsible;
        selected.pipeline = old.pipeline;
        selected.stage = old.stage;
        isSelected.value = false;
        nextTick(() => { isCanceling.value = false; });
    }
</script>

<style scoped> 

  :deep(.err) {
    color: #d93d42;
    line-height: 1.4;
    font-weight: 500;
    font-size: 12px;
  }

  .deal-top__selectors {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .deal-btns {
    margin-top: 10px;
  }

  .deal-btn {
    border: none;
    cursor: pointer;
    background: none;
    color: rgb(80, 80, 80);
    margin-left: 10px;
  }

  .deal-select {
    cursor: pointer;
    min-height: 45px;
    margin-top: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0 13px;
    color: #344054;
    background: #fff;
    border: 1px solid #dce2ea;
    border-radius: 8px;
    box-shadow: 0 1px 2px rgba(16,24,40,.02);
  }

  .deal-select__label {
    color: #768196;
    font-size: 12px;
    width: 450px;
  }


  @media (max-width: 700px) {
    .deal-select {
        width: 100%;
    }
    .deal-top__selectors {
        width: 100%;
    }
  }
</style>