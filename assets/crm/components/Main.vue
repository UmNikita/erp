<template>
    <h1>Kanban</h1>
    <Kanban
        v-if="pipelines.length"
        :pipelines="pipelines"
    />
    <EmptyPipelines v-else />
</template>

<script setup lang="ts">
    import Kanban from './Kanban/Kanban.vue';
    import EmptyPipelines from './Kanban/EmptyPipelines.vue';
    import { Pipeline } from '../types/pipeline.ts';
    import { onMounted, ref } from 'vue';
    import { getPipelines } from '../api/pipeline.ts';

    const pipelines = ref<Pipeline[]>([]);

    onMounted(async () => {
        pipelines.value = await getPipelines();
    })

</script>