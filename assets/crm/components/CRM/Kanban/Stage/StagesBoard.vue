<template>
    <section class="board-wrap">
        <div class="board" v-if="kanban?.stages.length">
        <Stage v-for="stage in kanban?.stages" :key="stage.id" @delete="acceptDeleteStage"
        :pipeline-id="pipelineId" :stage="stage" @lead-drop="onLeadDrop" />
        <div class="stage-btns">
            <button class="round-btn" @click="openModal(MODALS.CREATE_STAGE)"><PlusIco /></button>
        </div>
        </div>
        <EmptyStages v-else />
    </section>
</template>

<script setup lang="ts">
    import { deleteStage } from '../../../../api/stage.ts';
    import { usePipelines } from '../../../../composables/CRM/pipelines/usePipelines.ts';
    import { MODALS, useModal } from '../../../../composables/useModal';
    import { Kanban } from '../../../../types/kanban.ts';
    import PlusIco from '../../../icons/PlusIco.vue';
    import EmptyStages from '../Empty/EmptyStages.vue';
    import Stage from './Stage.vue';

    const { openModal } = useModal();

    const props = defineProps<{
        kanban: Kanban;
        pipelineId: number;
        onLeadDrop: any;
    }>();

    const { deleteStageForDetailPipeline } = usePipelines();

    async function acceptDeleteStage(id: number) {
        try {
            await deleteStage(id);
        }
        catch {
            alert("Не удалось удалить. Попробуйте позже!")
        }

        if (!props.kanban)
            return;

        props.kanban.stages = props.kanban.stages.filter(stage => stage.id !== id);
        deleteStageForDetailPipeline(props.pipelineId, id);
    }
</script>