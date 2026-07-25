<template>
    <div class="pipelines">
        <PipelineBtn v-for="pipeline in pipelines" :key="pipeline.id" :pipeline="pipeline" />
    </div>
    <div class="stages">
        <Stage v-for="stage in kanban?.stages" :key="stage.id" :stage="stage" />
    </div>
    <div>
        <KanbanStatistic v-if="kanban" :kanban="kanban" />
    </div>
</template>

<script setup lang="ts">
    import PipelineBtn from './PipelineBtn.vue';
    import type { Pipeline } from '../../types/pipeline';
    import { onMounted, ref } from 'vue';
    import { Kanban } from '../../types/kanban.ts';
    import { getKanban } from '../../api/kanban.ts';
    import KanbanStatistic from './KanbanStatistic.vue';
    import Stage from './Stage.vue';

    defineProps<{pipelines: Pipeline[]}>();

    const kanban = ref<Kanban | null>(null);

    onMounted(async () => {
        kanban.value = await getKanban(9);
    })
</script>

<style>
    .pipelines a {
        margin-left: 15px;
    }
    .stages {
        display: flex;
    }
</style>