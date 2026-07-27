<template>
    <router-link :to="kanbanPipelineUrl(pipeline.id)">{{pipeline.name}}</router-link>
</template>

<script setup lang="ts">
    import { kanbanPipelineUrl } from '../../routes/kanban';
    import type { Pipeline } from '../../types/pipeline';

    defineProps<{pipeline: Pipeline}>();
</script>