<template>
  <div class="crm">
    <PipelineNav />
    <PipelineList :pipeline-id="pipelineId" />
    <StagesBoard v-if="kanbanStore.kanban" />
    <section class="board-wrap" v-else></section>
    <KanbanStatistic v-if="kanbanStore.kanban" />
  </div>

  <StageModal v-if="activeModal === MODALS.CREATE_STAGE" @close="closeModal" :pipelineId="pipelineId" />
  <LeadModal v-if="activeModal === MODALS.CREATE_LEAD" @close="closeModal" />
</template>

<script setup lang="ts">
  import PipelineList from '../Pipeline/PipelineList.vue';
  import KanbanStatistic from './KanbanStatistic.vue';
  import PipelineNav from '../Pipeline/PipelineNav.vue';
  import StageModal from '../../modals/StageModal.vue';
  import StagesBoard from '../Stage/StagesBoard.vue';
  import { useInitPipelineKanban } from '../../../../composables/CRM/pipelines/useInitPipelineKanban.ts';
  import { useKanbanStore } from '../../../../stores/kanban.ts';
  import { MODALS, useModal } from '../../../../composables/useModal.ts';
  import LeadModal from '../../modals/LeadModal.vue';

  const kanbanStore = useKanbanStore();
  const {activeModal, closeModal} = useModal();
  
  const { pipelineId } = useInitPipelineKanban();

</script>

<style scoped>

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

  .nav-actions {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 14px;
    padding-left: 24px;
  }

  @media (max-width: 980px) {

    .crm {
      min-height: 100vh;
      border-radius: 0;
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

    .board {
      min-width: 1320px;
    }
  }

  .stages {
    display: flex;
  }
</style>