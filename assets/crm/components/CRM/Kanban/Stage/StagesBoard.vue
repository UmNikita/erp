<template>
    <section class="board-wrap">
        <template v-if="kanban?.stages.length">
            <div class="board" >
                <Stage v-for="stage in kanban?.stages" :key="stage.id" @delete="acceptDeleteStage" @back="moveBackStage"
                :pipeline-id="pipelineId" :stage="stage" @lead-drop="onLeadDrop" @forward="moveForwardStage" />
                <div class="stage-btns">
                    <button class="round-btn" @click="openModal(MODALS.CREATE_STAGE)"><PlusIco /></button>
                </div>
            </div>
            <LeadResult />
        </template>
        <EmptyStages v-else />
    </section>
</template>

<script setup lang="ts">
    import { deleteStage } from '../../../../api/stage.ts';
    import { usePipelines } from '../../../../composables/CRM/pipelines/usePipelines.ts';
    import { useMoveStage } from '../../../../composables/CRM/stages/useMoveStage.ts';
    import { MODALS, useModal } from '../../../../composables/useModal';
    import { Kanban } from '../../../../types/kanban.ts';
    import { StageUI } from '../../../../types/stage.ts';
    import PlusIco from '../../../icons/PlusIco.vue';
    import EmptyStages from '../Empty/EmptyStages.vue';
    import LeadResult from '../Lead/LeadResult.vue';
    import Stage from './Stage.vue';

    const { openModal } = useModal();

    const props = defineProps<{
        kanban: Kanban;
        pipelineId: number;
        onLeadDrop: any;
    }>();

    const { deleteStageForDetailPipeline } = usePipelines();
    const { moveForwardStage, moveBackStage } = useMoveStage();

    async function acceptDeleteStage(id: number) {
        try {
            await deleteStage(id);
        }
        catch {
            alert("Не удалось удалить. Попробуйте позже!");
            return;
        }

        if (!props.kanban)
            return;

        props.kanban.stages = props.kanban.stages.filter(stage => stage.id !== id);
        deleteStageForDetailPipeline(props.pipelineId, id);
    }
</script>

<style>
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
    @media (max-width: 980px) {

      .board {
        min-width: 1320px;
      }

      .round-btn {
        width: 42px;
        height: 42px;
      }
    }
</style>