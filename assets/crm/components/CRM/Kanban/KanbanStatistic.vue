<template>
    <div class="summary">
        <div class="summary-label">Итого по воронке:</div>
        <div class="summary-value">{{ kanban.leadsCount }} {{getPluralizeLead(kanban.leadsCount)}}</div>
        <div class="summary-value">{{ kanban.moneyAmount }} ₽</div>
        <button class="more-btn" type="button" aria-label="Дополнительные действия">•••</button>
    </div>
</template>

<script setup lang="ts">
    import { Kanban } from '../../../types/kanban';
    import { getPluralizeLead } from '../../../utils/words';

    defineProps<{kanban: Kanban}>();
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

    .more-btn {
      width: 44px;
      height: 42px;
      margin-left: auto;
      place-items: center;
      border: 1px solid var(--line);
      border-radius: 10px;
      background: #fff;
      color: #536071;
      font-weight: 700;
      letter-spacing: 2px;
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