<template>
  <section class="column" :style="{ '--accent': stage.color }" @dragover.prevent @drop="dropLead">
    <StageHeader :stage="stage" />
    <div class="column-body" v-if="stage?.leads.length">
      <Lead v-for="lead in stage?.leads" :key="lead.id" :lead="lead" :stage-id="stage.id" />
    </div>
    <EmptyLeads v-else />
  </section>
</template>

<script setup lang="ts">
  import { useMoveLead } from '../../../../composables/CRM/leads/useMoveLead.ts';
  import { StageUI } from '../../../../types/stage.ts';
  import EmptyLeads from '../Empty/EmptyLeads.vue';
  import Lead from '../Lead/Lead.vue';
  import StageHeader from './StageHeader.vue';

  const { onLeadDrop } = useMoveLead();

  const props = defineProps<{ stage: StageUI }>();

  async function dropLead(event: DragEvent) {
    const leadId = Number(event.dataTransfer?.getData('leadId'));
    const leadStageId = Number(event.dataTransfer?.getData('stageId'));
    const toStage = props.stage.id;

    if(leadStageId != toStage) {
      onLeadDrop(leadId, leadStageId, toStage);
    }   
  }
</script>

<style>
  .column {
    --accent: var(--blue);
    position: relative;
    flex-direction: column;
    border: 1px solid var(--line);
  }

  .column::before {
    content: "";
    position: absolute;
    right: -1px;
    left: -1px;
    height: 5px;
    background: var(--accent);
  }

  .column-header {
    padding: 26px 16px 20px;
  }

  .column-header-title {
    display: flex;
    justify-content: space-between;
  }

  .column-title {
    margin: 0 0 8px;
    font-size: 18px;
    max-width: 240px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .column-inp {
    margin: 0 0 8px;
    font-size: 18px;
    outline: none;
    border: none;
    border-bottom: 1px solid gray;  
  }

  .column-meta {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
  }

  .column-body {
    height: 450px;
    padding: 0 4px 12px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #cfd5df transparent;
  }
</style>