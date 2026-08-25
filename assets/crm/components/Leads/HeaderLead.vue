<template>
  <div class="deal-top" v-if="pipelines.length > 0">
    <button class="deal-top__back">
      <svg viewBox="0 0 24 24" fill="none"><path d="M15 5L8 12L15 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
      Назад к воронке
    </button>

    <div class="deal-top__title-row">
      <div class="deal-top__title-wrap">
        <RenameField title-field="" :value-field="lead.name" 
          class="deal-top__title" :editing="editing" :error="errors.name" v-model="data.name" />

        <button v-if="!editing" @click="startEditing" class="deal-top__edit">
          <svg viewBox="0 0 24 24" fill="none"><path d="M14 5L19 10M4 20L7.5 19.3L19 7.8A2.1 2.1 0 0 0 16.2 5L4.7 16.5L4 20Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
        </button>
        <div v-else>
          <button class="link accept" @click="acceptEditing">Принять</button>
          <button class="link" @click="cancelEditing">Отменить</button>
        </div>
      </div>

      <button class="deal-top__favorite">
        <svg viewBox="0 0 24 24" fill="none"><path d="M12 3L14.8 8.7L21 9.6L16.5 14L17.6 20.2L12 17.3L6.4 20.2L7.5 14L3 9.6L9.2 8.7L12 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
      </button>
    </div>

    <div class="deal-top__controls">
      <div class="deal-top__selectors">
        <StageField class="deal-select__label" title="Этап" :pipelines-detail="pipelines" 
          v-model:selected-stage-id="selectedStageId" v-model:selected-pipeline-id="selectedPipelineId" 
          :error="errors.stage_id" />
        <select class="deal-select" v-model="selectedResponsibleId">
          <option :value="null" disabled>Выбирите менеджера</option>
          <option v-for="responsible in responsibles"
          :key="responsible.id" :value="responsible.id">{{ responsible.name }}</option>
        </select>
        <div v-if="isSelected" class="deal-btns">
          <button class="deal-btn" @click="acceptEditingList">Принять</button>
          <button class="deal-btn" @click="cancelEditingList">Отменить</button>
        </div>
      </div>

      <div class="deal-top__communication">
        <button class="communication-button" type="button">
          <svg viewBox="0 0 24 24" fill="none"><path d="M7 4L10 8L8 11C9.4 14 10.8 15.4 13.8 16.8L17 15L20 18C20 19.1 19.1 20 18 20C10.3 19.5 4.5 13.7 4 6C4 4.9 4.9 4 6 4H7Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
          Позвонить
          <span class="communication-button__arrow">⌄</span>
        </button>

        <button class="communication-button" type="button">
          <svg viewBox="0 0 24 24" fill="none"><path d="M20 11.5A8 8 0 0 1 8.3 18.6L4 20L5.4 15.7A8 8 0 1 1 20 11.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
          Написать
          <span class="communication-button__arrow">⌄</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { useLeadForm } from '../../composables/CRM/leads/useLeadForm.ts';
  import { useInlineRenameForm } from '../../composables/useInlineRenameForm.ts';
  import { LeadDetail } from '../../types/lead';
  import RenameField from '../common/RenameField.vue';
  import { PipelineDetail } from '../../types/pipeline.ts';
  import { nextTick, onMounted, ref, watch } from 'vue';
  import StageField from '../CRM/modals/fields/StageField.vue';
  import { Responsible } from '../../types/kanban.ts';
  import { updateResponsibleLead, updateStageLead } from '../../api/lead.ts';

  const props = defineProps<{
    lead: LeadDetail;
    pipelines: PipelineDetail[];
    responsibles: Responsible[];
  }>();

  const isSelected = ref(false);
  const selectedResponsibleId = ref<number | null>(props.lead.responsible?.id ? props.lead.responsible.id : null);
  const oldSelectedResponsibleId = ref<number | null>(null);
  const selectedStageId = ref<number | null>();
  const selectedPipelineId = ref<number | null>(null);
  const oldSelectedStageId = ref<number | null>(null);
  const oldSelectedPipelineId = ref<number | null>(null);

  const { updateLead } = useLeadForm();

  const {editing, errors, data, generalError, 
    startEditing, cancelEditing, accept, setNewData} = useInlineRenameForm(props.lead);
  

  const initialized = ref(false);
  const isCanceling = ref(false);

  onMounted(() => {
    oldSelectedResponsibleId.value = selectedResponsibleId.value;
    props.pipelines.forEach(pipeline => {
      const stage = pipeline.stages.find(stage => stage.id === props.lead.stage.id);

      if (stage) {
        selectedPipelineId.value = pipeline.id;
        oldSelectedPipelineId.value = pipeline.id;

        selectedStageId.value = stage.id;
        oldSelectedStageId.value = stage.id;

        initialized.value = true;
      }
    });
  });

  async function acceptEditingList() {
    if(selectedStageId.value == oldSelectedStageId.value && selectedResponsibleId.value == oldSelectedResponsibleId.value) {
      isSelected.value = false;
      return;
    }
    if(selectedResponsibleId.value) {
      try{
        await updateResponsibleLead(props.lead.id, selectedResponsibleId.value);
      } catch {
        alert("Возникла ошибка!");
      }
      isSelected.value = false;
      oldSelectedResponsibleId.value = selectedResponsibleId.value;
    }
    if(selectedStageId.value) {
      try{
        await updateStageLead(props.lead.id, selectedStageId.value);
      } catch {
        alert("Возникла ошибка!");
      }
      isSelected.value = false;
      oldSelectedPipelineId.value = oldSelectedPipelineId.value;
      oldSelectedStageId.value = selectedStageId.value;
    }
  }

  function cancelEditingList() {
    isCanceling.value = true;

    selectedPipelineId.value = oldSelectedPipelineId.value;
    selectedStageId.value = oldSelectedStageId.value;

    selectedResponsibleId.value = oldSelectedResponsibleId.value;

    isSelected.value = false;

    nextTick(() => {
      isCanceling.value = false;
    });
  }

  async function acceptEditing() {
    const newData = accept();
    if(newData == null)
      return;
    if(!props.lead)
      return;
    const res = await updateLead(props.lead, data.value, errors, generalError);
    if(res) {
      editing.value = false;
      setNewData();
    }
  }

  watch([selectedStageId, selectedPipelineId, selectedResponsibleId], () => {
    if (isCanceling.value) {
      return;
    }

    if (
      selectedStageId.value !== oldSelectedStageId.value ||
      selectedPipelineId.value !== oldSelectedPipelineId.value ||
      selectedResponsibleId.value !== oldSelectedResponsibleId.value
    ) {
      isSelected.value = true;
    }
  });

</script>

<style scoped> 

  .link {
    padding: 0;
    border: 0;
    background: transparent;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
  }

  .accept {
    margin-right: 15px;
  }
  .general-err {
    margin: 6px 0 0;
    padding: 4px 6px 4px 16px;

    color: #c9363e;
    background: #fff5f5;
    border: 1px solid #f2c9cc;
    border-radius: 8px;

    font-size: 10px;
    font-weight: 500;
  }

  .deal-top {
    padding: 22px 28px;
    background: #fff;
    border: 1px solid #e4e8ee;
    border-radius: 14px;
    box-shadow: 0 3px 12px rgba(28, 39, 56, .04);
    display: block;
  }

  .deal-top__back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 13px;
    padding: 0;
    color: #1267f4;
    background: transparent;
    border: 0;
    font-size: 12px;
    font-weight: 600;
  }

  .deal-top__back svg {
    width: 16px;
    height: 16px;
  }

  .deal-top__title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 17px;
  }

  .deal-top__title-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .deal-top__title {
    margin: 0;
    font-size: 27px;
    line-height: 1.2;
    font-weight: 700;
    letter-spacing: -.5px;
  }

  :deep(.deal-top__title input) {
    border: none;
    outline: none;
    border-bottom: 1px solid #303744;
    width: 200px;
  }

  :deep(.err) {
    color: #d93d42;
    line-height: 1.4;
    font-weight: 500;
  }

  .deal-top__edit,
  .deal-top__favorite {
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    color: #465267;
    background: transparent;
    border: 0;
    width: 25px;
    height: 25px;
    border-radius: 6px;
  }
  
  .deal-top__edit:hover {
    background: #f1f5fa;
  }

  .deal-top__edit svg {
    width: 17px;
    height: 17px;
  }

  .deal-top__favorite svg {
    width: 21px;
    height: 21px;
  }

  .deal-top__controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
  }

  .deal-top__selectors,
  .deal-top__communication {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .stage-dot {
    width: 8px;
    height: 8px;
    background: #16a565;
    border-radius: 50%;
  }

  .deal-select {
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

  .deal-select__icon {
    width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #4e627d;
  }

  .deal-select__icon svg {
    width: 18px;
    height: 18px;
  }

  .deal-select__label {
    color: #768196;
    font-size: 12px;
    width: 450px;
  }

  .deal-select__value {
    font-size: 12px;
    font-weight: 650;
  }

  .deal-select__arrow {
    width: 15px;
    height: 15px;
    margin-left: 2px;
    color: #4c5c73;
  }

  .communication-button {
    height: 40px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 0 14px;
    color: #273247;
    background: #fff;
    border: 1px solid #dce2ea;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
  }

  .communication-button:hover {
    background: #f8f9fb;
  }

  .communication-button svg {
    width: 18px;
    height: 18px;
  }

  .communication-button__arrow {
    padding-left: 11px;
    margin-left: 4px;
    border-left: 1px solid #e1e5eb;
  }

  @media (max-width: 900px) {

  .deal-top__controls {
    align-items: flex-start;
    flex-direction: column;
  }

  .deal-top__communication {
    width: 100%;
  }
  }

  @media (max-width: 700px) {

  .deal-top {
    padding: 18px 15px;
  }

  .deal-top__title {
    font-size: 23px;
  }

  .deal-select {
    width: 100%;
  }

  .deal-top__selectors {
    width: 100%;
  }

  .communication-button {
    flex: 1;
    justify-content: center;
  }
  }
</style>