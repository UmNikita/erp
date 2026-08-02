<template>
    <router-link v-if="props.isCurrent" class="tab active" :to="kanbanPipelineUrl(pipeline.id)">{{pipeline.name}}</router-link>
    <router-link v-else class="tab" :to="kanbanPipelineUrl(pipeline.id)">{{pipeline.name}}</router-link>
</template>

<script setup lang="ts">
    import { kanbanPipelineUrl } from '../../../../routes/kanban';
    import type { Pipeline } from '../../../../types/pipeline';

    const props = defineProps<{
      pipeline: Pipeline,
      isCurrent: boolean
    }>();
</script>

<style>
    .tab {
      position: relative;
      min-width: 215px;
      text-align: center;
      margin-top: auto;
      margin-bottom: auto;
    }

    .tab.active {
      color: #5c79c6;
      font-weight: 600;
    }
</style>