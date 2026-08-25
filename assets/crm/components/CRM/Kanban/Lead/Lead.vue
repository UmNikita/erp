<template>
  <router-link :to="getCurrentLead(lead.id)" class="deal" draggable="true" @dragstart="onDragStart">
    <div class="deal-top">
      <div class="deal-title">{{lead.name}}</div>
      <time class="deal-date">{{ lead.date }}</time>
    </div>
    <div class="deal-person">{{lead.client}}</div>
    <div class="deal-bottom">
      <div class="deal-amount">{{ formatNumber(lead.moneyAmount) }} ₽</div>
      <div class="assignee">{{lead.manager}}</div>
    </div>
  </router-link>
</template>

<script setup lang="ts">
  import { getCurrentLead } from '../../../../routes/lead';
  import { Lead } from '../../../../types/lead';
  import { formatNumber } from '../../../../utils/fields';
  
  const props = defineProps<{lead: Lead, stageId: number}>();

  function onDragStart(event: DragEvent) {
    event.dataTransfer?.setData('leadId', String(props.lead.id));
    event.dataTransfer?.setData('stageId', String(props.stageId));
  }
</script>

<style>
    .deal {
      display: block;
      padding: 18px 16px 15px;
      border: 1px solid #e6e9ee;
      border-radius: 11px;
      cursor: grab;
      transition: transform 0.16s ease, box-shadow 0.16s ease, opacity 0.16s ease;
    }

    .deal:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 20px rgba(31, 45, 68, 0.11);
    }

    .deal-top,
    .deal-bottom {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .deal-title {
      font-size: 15px;
      font-weight: 650;
    }

    .deal-date {
      color: #5f6878;
      font-size: 13px;
    }

    .deal-person {
      margin: 23px 0 10px;
      font-size: 14px;
    }

    .deal-amount {
      color: #4b5565;
      font-size: 14px;
    }

    .assignee {
      max-width: 105px;
      padding: 8px 11px;
      border-radius: 8px;
      background: #f3f4f7;
      color: #343a45;
      font-size: 13px;
      line-height: 1;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
</style>