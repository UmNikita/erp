<template>
  <div class="deal-page" v-if="loading">
    <Error v-if="error" />
    <template v-else>
      <HeaderLead :lead="lead" />
      <div class="deal-layout">
        <InformationLead :lead="lead" />
        <SidebarLead :lead="lead" />
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
  import { onMounted, ref } from 'vue';
  import { LeadDetail, Message } from '../../types/lead';
  import HeaderLead from './Header/HeaderLead.vue';
  import InformationLead from './Information/InformationLead.vue';
  import SidebarLead from './SidebarLead/SidebarLead.vue';
  import { getLeadDetail } from '../../api/lead.ts';
  import { useRoute } from 'vue-router';
  import Error from '../Error.vue';
  import { usePipelineStore } from '../../stores/pipelines.ts';
  import { useResponsiblesStore } from '../../stores/responsibles.ts';

  const lead = ref<LeadDetail>();
  const loading = ref(false);
  const error = ref(false);
  const leadMessages = ref<Message[]>([]);

  const route = useRoute();
  const leadId = Number(route.params.id);

  const pipelineStore = usePipelineStore();
  const responsiblesStore = useResponsiblesStore();

  onMounted(async ()=> {
    try{
      lead.value = await getLeadDetail(leadId);
      await responsiblesStore.loadResponsibles();
      await pipelineStore.loadPipelines();
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