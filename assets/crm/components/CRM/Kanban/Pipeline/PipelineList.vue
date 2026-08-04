<template>
  <nav class="navrow">
      <div class="tabs">
        <template v-for="pipeline in pipelines" :key="pipeline.id">
          <router-link v-if="pipelineId == pipeline.id" class="tab active" :to="kanbanPipelineUrl(pipeline.id)">{{pipeline.name}}</router-link>
          <router-link v-else class="tab" :to="kanbanPipelineUrl(pipeline.id)">{{pipeline.name}}</router-link>
        </template>
      </div>
      <div class="nav-actions">
        <button class="round-btn" @click="openModal(MODALS.CREATE_PIPELINE)"><PlusIco /></button>
        <button class="round-btn" @click="openModal(MODALS.SETTINGS_PIPELINE)"><SettingsIco /></button>
      </div>
  </nav>
</template>

<script setup lang="ts">
    import { MODALS, useModal } from '../../../../composables/useModal';
    import { kanbanPipelineUrl } from '../../../../routes/kanban';
    import type { Pipeline } from '../../../../types/pipeline';
    import PlusIco from '../../../icons/PlusIco.vue';
    import SettingsIco from '../../../icons/SettingsIco.vue';

    const { openModal } = useModal();

    const props = defineProps<{
      pipelines: Pipeline[];
      pipelineId: number;
    }>();
</script>

<style>
    .tabs {
      min-width: 0;
      display: flex;
      align-items: stretch;
      gap: 4px;
    }

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