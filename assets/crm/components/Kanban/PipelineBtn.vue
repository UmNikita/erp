<template>
    <a :href="kanbanPipelineUrl(pipeline.id)">{{pipeline.name}}</a>
</template>

<script setup lang="ts">
    import { kanbanPipelineUrl } from '../../routes/kanban';
    import type { Pipeline } from '../../types/pipeline';

    defineProps<{pipeline: Pipeline}>();
</script>