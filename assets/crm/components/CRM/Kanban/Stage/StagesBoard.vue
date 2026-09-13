<template>
  <section class="board-wrap">
    <template v-if="kanbanStore.kanban?.stages.length">
      <div class="board" >
        <Stage v-for="stage in kanbanStore.kanban?.stages"
        :key="stage.id" :stage="stage" />
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
  import { MODALS, useModal } from '../../../../composables/useModal';
  import { useKanbanStore } from '../../../../stores/kanban.ts';
  import PlusIco from '../../../icons/PlusIco.vue';
  import EmptyStages from '../Empty/EmptyStages.vue';
  import LeadResult from '../Lead/LeadResult.vue';
  import Stage from './Stage.vue';

  const kanbanStore = useKanbanStore();
  const { openModal } = useModal();
</script>

<style scoped>
  .board-wrap {
    flex: 1;
    min-height: 0;
    padding: 26px 22px 18px;
    overflow-x: auto;
    overflow-y: hidden;
  }

  .board {
    height: 100%;
    min-height: 570px;
    display: grid;
    grid-template-columns: repeat(6, minmax(275px, 1fr));
    gap: 14px;
  }

  .board-wrap::-webkit-scrollbar {
    height: 6px;
  }

  .board-wrap::-webkit-scrollbar-track {
    background: #f1f1f1;
  }

  .board-wrap::-webkit-scrollbar-thumb {
    background: #1357c5;
    border-radius: 10px;
  }

  .board-wrap::-webkit-scrollbar-thumb:hover {
    background: #104aa8;
    cursor: pointer;
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
    cursor: pointer;
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
    
    .board-wrap {
      padding: 16px 12px;
    }
  }
</style>