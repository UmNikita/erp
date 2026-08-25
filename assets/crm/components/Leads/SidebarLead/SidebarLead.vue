<template>
    <aside class="deal-sidebar">
      <section class="deal-kpi">
        <h2 class="deal-kpi__title">Ключевые показатели</h2>

        <div class="deal-kpi__grid">
          <div class="kpi-card">
            <span class="kpi-card__label kpi-card__label--green">LTV клиента</span>
            <strong class="kpi-card__value">{{ lead.client?.ltv ? formatAmount(lead.client?.ltv) : '0' }} ₽</strong>
            <span class="kpi-card__sub kpi-card__sub--green">Средний чек {{ lead.client?.average_cheque ? formatAmount(lead.client?.average_cheque) : '0' }} ₽</span>
          </div>

          <div class="kpi-card">
            <span class="kpi-card__label kpi-card__label--blue">Сделок всего</span>
            <strong class="kpi-card__value">{{ lead.client?.count_leads ? lead.client?.count_leads : '0' }}</strong>
            <span class="kpi-card__sub kpi-card__sub--blue">На сумму {{ lead.client?.amount_sum_leads ? formatAmount(lead.client?.amount_sum_leads) : '0' }} ₽</span>
          </div>
        </div>
      </section>

      <ChatLead :lead-messages="leadMessages" :lead="lead" />
    </aside>
</template>

<script setup lang="ts">
  import { LeadDetail, Message } from '../../../types/lead.ts';
  import { formatAmount } from '../../../utils/fields.ts';
  import ChatLead from './ChatLead.vue';

  const props = defineProps<{
    lead: LeadDetail;
    leadMessages: Message[];
  }>();

</script>

<style scoped>

  .deal-sidebar {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .deal-kpi {
    background: #fff;
    border: 1px solid #e4e8ee;
    border-radius: 13px;
    box-shadow: 0 2px 8px rgba(28,39,56,.025);
  }

  .deal-kpi {
    padding: 17px;
  }

  .deal-kpi__title {
    margin: 0 0 13px;
    font-size: 12px;
    font-weight: 650;
    text-transform: uppercase;
  }

  .deal-kpi__grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
  }

  .kpi-card {
    padding: 16px;
    border: 1px solid #e1e5eb;
    border-radius: 9px;
    background: #fff;
  }

  .kpi-card__label {
    display: block;
    margin-bottom: 15px;
    font-size: 11px;
    font-weight: 600;
  }

  .kpi-card__label--green {
    color: #178a52;
  }

  .kpi-card__label--blue {
    color: #1768c9;
  }

  .kpi-card__value {
    display: block;
    margin-bottom: 8px;
    color: #111827;
    font-size: 22px;
    font-weight: 600;
  }

  .kpi-card__sub {
    font-size: 11px;
    font-weight: 500;
  }

  .kpi-card__sub--green {
    color: #16985a;
  }

  .kpi-card__sub--blue {
    color: #1768c9;
  }

  .activity-avatar {
    width: 31px;
    height: 31px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #7a8496;
    background: #e9edf2;
    border-radius: 50%;
  }

  .activity-avatar svg {
    width: 18px;
    height: 18px;
  }

  @media (max-width: 1200px) {
    .deal-sidebar {
      display: grid;
      grid-template-columns: 1fr 1.2fr;
    }
  }

  @media (max-width: 700px) {
    .deal-sidebar {
      grid-template-columns: 1fr;
    }
  }
</style>