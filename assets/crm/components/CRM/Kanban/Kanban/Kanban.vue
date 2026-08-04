<template>
    <div class="crm">
      <PipelineNav />
      <PipelineList :pipelines="pipelines" :pipeline-id="pipelineId" />
      <StagesBoard :kanban="kanban" :pipelineId="pipelineId"
        :onLeadDrop="onLeadDrop" v-if="kanban" 
      />
      <section class="board-wrap" v-else></section>
      <KanbanStatistic v-if="kanban" :kanban="kanban" />
    </div>

    <KanbanModals :pipelines-detail="pipelinesDetail" :pipeline-id="pipelineId" />
    
</template>

<script setup lang="ts">
  import PipelineList from '../Pipeline/PipelineList.vue';
  import type { Pipeline, PipelineDetail } from '../../../../types/pipeline';
  import KanbanStatistic from './KanbanStatistic.vue';
  import PipelineNav from '../Pipeline/PipelineNav.vue';
  import { usePipelineKanban } from '../../../../composables/CRM/pipelines/usePipelineKanban.ts';
  import { useMoveLead } from '../../../../composables/CRM/leads/useMoveLead.ts';
  import StagesBoard from '../Stage/StagesBoard.vue';
  import { useInitPipelineKanban } from '../../../../composables/CRM/pipelines/useInitPipelineKanban.ts';
  import KanbanModals from './KanbanModals.vue';

  const props = defineProps<{
    pipelines: Pipeline[],
    pipelinesDetail: PipelineDetail[],
    setError: () => void;
  }>();
  
  const {kanban, getAllResponsibles, loadKanban } = usePipelineKanban();

  const { onLeadDrop } = useMoveLead(kanban);
  
  const { pipelineId } = useInitPipelineKanban(props.pipelines, getAllResponsibles, loadKanban, props.setError);

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

    .nav-actions {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: 14px;
      padding-left: 24px;
    }

    .board-wrap {
      flex: 1;
      min-height: 0;
      padding: 26px 22px 18px;
      overflow-x: auto;
      overflow-y: hidden;
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