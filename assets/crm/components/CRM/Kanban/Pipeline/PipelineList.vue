<template>
  <nav class="navrow">
    <div class="tabs" ref="tabsElement">
      <template v-for="pipeline in pipelineStore.pipelines" :key="pipeline.id">
        <router-link 
        :class="pipelineId == pipeline.id ? 'tab active' : 'tab'"
        :to="kanbanPipelineUrl(pipeline.id)" >
          {{pipeline.name}}
        </router-link>
      </template>
    </div>
    <div class="nav-actions">
      <button class="round-btn" @click="openModal(MODALS.CREATE_PIPELINE)"><PlusIco /></button>
      <button class="round-btn" @click="openModal(MODALS.SETTINGS_PIPELINE)"><SettingsIco /></button>
    </div>
  </nav>
</template>

<script setup lang="ts">
  import { nextTick, onMounted, ref } from 'vue';
  import { MODALS, useModal } from '../../../../composables/useModal';
  import { kanbanPipelineUrl } from '../../../../routes/kanban';
  import { usePipelineStore } from '../../../../stores/pipelines.ts';
  import PlusIco from '../../../icons/PlusIco.vue';
  import SettingsIco from '../../../icons/SettingsIco.vue';

  const { openModal } = useModal();
  const pipelineStore = usePipelineStore();

  const tabsElement = ref<HTMLElement | null>(null);

  onMounted(async () => {
    await scroll();
  });

  async function scroll() {
    await nextTick();
    const activeTab = tabsElement.value?.querySelector('.active');
    if (!activeTab || !tabsElement.value) {
      return;
    }
    const container = tabsElement.value;
    const scrollLeft = activeTab.getBoundingClientRect().left
      - container.getBoundingClientRect().left
      + container.scrollLeft;
    container.scrollTo({
      left: scrollLeft,
      behavior: 'smooth',
    });
  }

  const props = defineProps<{
    pipelineId: number;
  }>();
</script>

<style scoped>
  .tabs {
    min-width: 0;
    display: flex;
    align-items: stretch;
    gap: 4px;
    max-width: 89%;
    overflow: auto;
  }

  .tabs {
    overflow-x: auto;
  }

  .tabs::-webkit-scrollbar {
      height: 6px;
  }

  .tabs::-webkit-scrollbar-track {
      background: #f1f1f1;
  }

  .tabs::-webkit-scrollbar-thumb {
      background: #1357c5;
      border-radius: 10px;
  }

  .tabs::-webkit-scrollbar-thumb:hover {
      background: #104aa8;
      cursor: pointer;
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

  .navrow {
    min-height: 72px;
    display: flex;
    justify-content: space-between;
    align-items: stretch;
    padding: 0 28px;
    border-bottom: 1px solid var(--line);
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
    cursor: pointer;
    margin: 10px;
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

    .tabs {
      overflow: visible;
    }

    .navrow {
      min-height: 64px;
      padding: 0 12px;
      overflow-x: auto;
    }

    .round-btn {
      width: 42px;
      height: 42px;
    }

  }
</style>