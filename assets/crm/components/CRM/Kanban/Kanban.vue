<template>
    <div class="crm">
      <PipelineNav @open-lead-modal="openModal(MODALS.CREATE_LEAD)" />
      <nav class="navrow">
          <div class="tabs">
              <PipelineBtn v-for="pipeline in pipelines" :key="pipeline.id" :is-current="pipelineId == pipeline.id" :pipeline="pipeline" />
          </div>
          <div class="nav-actions">
            <button class="round-btn" @click="emit('openPipelineModal')"><PlusIco /></button>
            <button class="round-btn" @click="emit('openPipelineEditModal')"><SettingsIco /></button>
          </div>
      </nav>
      <section class="board-wrap" v-if="loading">
          <div class="board" v-if="kanban?.stages.length">
            <Stage v-for="stage in kanban?.stages" :key="stage.id" @delete="acceptDeleteStage"
            @rename="acceptRenameStage" :pipeline-id="pipelineId" :stage="stage" @lead-drop="onLeadDrop" />
            <div class="stage-btns">
              <button class="round-btn" @click="openModal(MODALS.CREATE_STAGE)"><PlusIco /></button>
            </div>
          </div>
          <EmptyStages @open-pipeline-modal="openModal(MODALS.CREATE_STAGE)" v-else />
      </section>
      <section class="board-wrap" v-else></section>
      <KanbanStatistic v-if="kanban" :kanban="kanban" />
    </div>

    <StageModal
      v-if="activeModal === MODALS.CREATE_STAGE" :pipeline_id="pipelineId"
      @close="closeModal" @submit="acceptAddStage" :error="generalError"
    />

    <LeadModal
      v-if="activeModal === MODALS.CREATE_LEAD" :pipelinesDetail="pipelinesDetail"
      @close="closeModal" @submit="acceptAddLead" :error="generalError" :responsibles="responsibles"
    />
    
</template>

<script setup lang="ts">
  import PipelineBtn from './Pipeline/PipelineBtn.vue';
  import type { Pipeline, PipelineDetail } from '../../../types/pipeline';
  import KanbanStatistic from './KanbanStatistic.vue';
  import EmptyStages from './Empty/EmptyStages.vue';
  import Stage from './Stage/Stage.vue';
  import { createStageLead, updateStageLead } from '../../../api/lead.ts';
  import StageModal from '../modals/StageModal.vue';
  import { Stage as StageType, StageRequest } from '../../../types/stage.ts';
  import PipelineNav from './Pipeline/PipelineNav.vue';
  import { usePipelineKanban } from '../../../composables/CRM/usePipelineKanban.ts';
  import { MODALS, useModal } from '../../../composables/useModal.ts';
  import PlusIco from '../../icons/PlusIco.vue';
  import SettingsIco from '../../icons/SettingsIco.vue';
  import { createStage, renameStage as renameThisStage, deleteStage as deleteCurrentStage } from '../../../api/stage.ts';
  import { responseToStage } from '../../../mappers/stageMapper.ts';
  import { useMoveLead } from '../../../composables/CRM/useMoveLead.ts';
  import LeadModal from '../modals/LeadModal.vue';
  import { LeadRequest } from '../../../types/lead.ts';

  const props = defineProps<{
    pipelines: Pipeline[],
    pipelinesDetail: PipelineDetail[],
    updatePipelinesDetail: (pipelineId: number, stage: StageType) => void,
    renameStagePipelinesDetail: (pipelineId: number, stageId: number, newName: string) => void,
    deleteStagePipelinesDetail: (pipelineId: number, stageId: number) => void,
    setError: () => void;
  }>();
  const {kanban, pipelineId, responsibles, loading,
  updateKanbanAfterCreateStage, addLeadKanban} = usePipelineKanban(props.pipelines, props.setError);

  const {activeModal, generalError, openModal, closeModal} = useModal();

  const {moveLead} = useMoveLead(kanban);

  async function onLeadDrop(leadId: number, fromStageId: number, toStageId: number) {
      moveLead(leadId, fromStageId, toStageId);
      try {
        await updateStageLead(leadId, toStageId);
      } catch(error) {
        console.error(error);
        moveLead(leadId, toStageId, fromStageId);
      }
  }

  async function acceptAddStage(data: StageRequest) {
    data.pipeline_id = pipelineId.value;
    if(data.pipeline_id == null) {
      generalError.value = "Ошибка. Перезагрузите страницу!";
      return;
    }
    const stage = await createStage(data);
    updateKanbanAfterCreateStage(stage);
    props.updatePipelinesDetail(data.pipeline_id, responseToStage(stage));
    closeModal();
  }

  async function acceptDeleteStage(id: number) {
    try {
      await deleteCurrentStage(id);
    }
    catch {
      alert("Не удалось удалить. Попробуйте позже!")
    }

    if (!kanban.value)
      return;

    kanban.value.stages = kanban.value.stages.filter(stage => stage.id !== id);
    props.deleteStagePipelinesDetail(pipelineId.value, id);
  }
  
  async function acceptRenameStage(prop: any, value: string) {
    const oldName = prop.stage.name;
    try {
      prop.stage.name = value;
      await renameThisStage(value, prop.stage.id);
    }
    catch {
        alert("Не удалось обновить название. Попробуйте позже!")
        prop.stage.name = oldName;
    }
    props.renameStagePipelinesDetail(pipelineId.value, prop.stage.id, value);
  }

  async function acceptAddLead(data: LeadRequest) {
    try {
      const lead = await createStageLead(data);
      addLeadKanban(lead);
      closeModal();
    }
    catch (error) {
      generalError.value = 'Не удалось добавить лид. Попробуйте чуть позже';
    }
  }

  const emit = defineEmits<{
      openPipelineModal: [],
      openPipelineEditModal: []
  }>()
</script>

<style>

    button {
      cursor: pointer;
    }

    .crm {
      width: 100%;
      min-height: calc(100vh - 100px);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      background: white;
      border: 1px solid var(--line);
      border-radius: var(--radius-lg);
      box-shadow: 0px 0px 26px 0px rgba(34, 60, 80, 0.3);
    }

    .navrow {
      min-height: 72px;
      display: flex;
      align-items: stretch;
      padding: 0 28px;
      border-bottom: 1px solid var(--line);
    }

    .tabs {
      min-width: 0;
      display: flex;
      align-items: stretch;
      gap: 4px;
    }

    .nav-actions {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: 14px;
      padding-left: 24px;
    }

    .round-btn {
      width: 49px;
      height: 49px;
      display: inline-grid;
      place-items: center;
      border: 1px solid var(--line);
      border-radius: 50%;
      background: #fff;
      color: #151922;
      transition: background 0.18s, border-color 0.18s, transform 0.18s;
    }

    .round-btn:hover {
      transform: translateY(-1px);
      border-color: #ccd3dd;
      background: #f8f9fb;
    }

    .round-btn svg {
      width: 22px;
      height: 22px;
    }

    .board-wrap {
      flex: 1;
      min-height: 0;
      padding: 26px 22px 18px;
      overflow-x: auto;
      overflow-y: hidden;
    }

    .board {
      min-width: 1860px;
      height: 100%;
      min-height: 570px;
      display: grid;
      grid-template-columns: repeat(6, minmax(210px, 1fr));
      gap: 14px;
    }

    .stage-btns {
      padding-top: 250px;
      padding-left: 25px;
    }

    @media (max-width: 980px) {

      .crm {
        min-height: 100vh;
        border-radius: 0;
      }

      .topbar {
        min-height: auto;
        flex-wrap: wrap;
        padding: 14px;
      }

      .search {
        flex-basis: 100%;
      }

      .search input {
        height: 52px;
        font-size: 16px;
      }

      .create-btn {
        height: 50px;
        width: 100%;
        margin-left: 0;
      }

      .navrow {
        min-height: 64px;
        padding: 0 12px;
        overflow-x: auto;
      }

      .tabs {
        overflow: visible;
      }

      .tab {
        min-width: auto;
        padding: 0 18px;
        font-size: 15px;
      }

      .nav-actions {
        position: sticky;
        right: 0;
        padding-left: 12px;
        background: linear-gradient(90deg, transparent, #fff 18%);
      }

      .round-btn {
        width: 42px;
        height: 42px;
      }

      .board-wrap {
        padding: 16px 12px;
      }

      .board {
        min-width: 1320px;
      }
    }

    .stages {
        display: flex;
    }
</style>