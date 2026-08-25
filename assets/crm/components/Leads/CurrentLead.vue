<template>
  <div class="deal-page" v-if="loading">
    <Error v-if="error" />
    <template v-else>
      <HeaderLead :responsibles="responsibles" :lead="lead" :pipelines="pipelines" />
      <div class="deal-layout">
        <InformationLead :lead="lead" />
        <SidebarLead :lead-messages="leadMessages" :lead="lead" />
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
  import { onMounted, ref } from 'vue';
  import { LeadDetail, Message } from '../../types/lead';
  import HeaderLead from './HeaderLead.vue';
  import InformationLead from './Information/InformationLead.vue';
  import SidebarLead from './SidebarLead/SidebarLead.vue';
  import { getLeadDetail, getLeadMessages } from '../../api/lead.ts';
  import { useRoute } from 'vue-router';
  import Error from '../Error.vue';
  import { PipelineDetail } from '../../types/pipeline.ts';
  import { getPipelinesDetail } from '../../api/pipeline.ts';
  import { Responsible } from '../../types/kanban.ts';
  import {getResponsibles} from '../../api/kanban.ts';

  const lead = ref<LeadDetail>();
  const loading = ref(false);
  const error = ref(false);
  const pipelines = ref<PipelineDetail[]>([]);
  const responsibles = ref<Responsible[]>([]);
  const leadMessages = ref<Message[]>([]);

  const route = useRoute();
  const leadId = Number(route.params.id);

  onMounted(async ()=> {
    try{
      lead.value = await getLeadDetail(leadId);
      pipelines.value = await getPipelinesDetail();
      responsibles.value = await getResponsibles();
      leadMessages.value = await getLeadMessages(lead.value.id);
    }
    catch {
      error.value = true;
    }
    finally {
      loading.value = true;
    }
  })

</script>

<style scoped> 
    .deal-page {
      width: 100%;
      min-height: 100vh;
      padding: 14px;
      background: #f4f6f9;
      color: #171b24;
      font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
      box-sizing: border-box;
    }

    .deal-page *,
    .deal-page *::before,
    .deal-page *::after {
      box-sizing: border-box;
    }

    button,
    input {
      font: inherit;
    }

    button {
      cursor: pointer;
    }

    .deal-layout {
      display: grid;
      grid-template-columns: minmax(0, 1.9fr) minmax(350px, 1fr);
      gap: 10px;
      margin-top: 10px;
    }

    @media (max-width: 1200px) {
      .deal-layout {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 700px) {
      .deal-page {
        padding: 8px;
      }

      .deal-sidebar {
        grid-template-columns: 1fr;
      }

      .deal-select {
        width: 100%;
      }
    }
</style>