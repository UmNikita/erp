<template>
    <div class="summary">
      <div class="summary-label">Итого по воронке:</div>
      <div class="summary-value">{{ kanbanStore.kanban.leadsCount }} {{getPluralizeLead(kanbanStore.kanban.leadsCount)}}</div>
      <div class="summary-value">{{ kanbanStore.kanban.moneyAmount }} ₽</div>
    </div>
</template>

<script setup lang="ts">
  import { useKanbanStore } from '../../../../stores/kanban';
  import { getPluralizeLead } from '../../../../utils/words';

  const kanbanStore = useKanbanStore();

</script>

<style>
  .summary {
    min-height: 63px;
    margin: 0 22px 22px;
    display: flex;
    align-items: center;
    padding: 0 22px 0 34px;
    border: 1px solid var(--line);
    border-radius: 12px;
    box-shadow: 0px 0px 15px -6px rgba(34, 60, 80, 0.3);
  }

  .summary-label {
    min-width: 270px;
    font-size: 17px;
  }

  .summary-value {
    min-width: 200px;
    padding: 0 45px;
    border-left: 1px solid var(--line);
    font-size: 16px;
  }

  @media (max-width: 980px) {
    .summary {
      margin: 0 12px 12px;
      padding-left: 18px;
      overflow-x: auto;
    }

    .summary-label {
      min-width: 220px;
    }

    .summary-value {
      min-width: 170px;
      padding: 0 24px;
    }
  }
</style>