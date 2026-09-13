<template>
  <CreateModalWrapper :accepting="accepting" title-btn="Создать" title="Создание сделки" subtitle="Заполните информацию о сделки"
  :error="generalError" @submit="submit" @close="emit('close')">
    <TextField :required="true" :ico="LeadIco" title="Название сделки" v-model="name" 
    placeholder="Введите название сделки" :error="errors.name" />
    <NumberField :required="false" :ico="LeadIco" title="Бюджет сделки" v-model="budget" :error="errors.budget" />
    <TextField :required="false" :ico="ProductIco" title="Продукт/Услуга" v-model="product" 
    placeholder="Введите название продукта/услуги" :error="errors.product" />
    <TextField :required="false" :ico="SourceIco" title="Источник" v-model="source" 
    placeholder="Введите название источника" :error="errors.source" />
    <TextField :required="false" :ico="NextActionIco" title="Следующие действие" v-model="next_action" 
    placeholder="Введите следующие действие" :error="errors.next_action" />
    <TextField :required="false" :ico="CommentIco" title="Комментарий" v-model="comment" 
    placeholder="Введите комментарий" :error="errors.comment" />
    <StageField title="Комментарий" :pipelines-detail="pipelineStore.pipelinesDetail" 
    v-model:selected-stage-id="selectedStageId" v-model:selected-pipeline-id="selectedPipelineId" :error="errors.stage_id" />
    <SelectField :required="false" :ico="ClientIco" title="Менеджер" v-model="currentManager" 
    :elements="responsibleStore.responsibles" firstElement="Выбирите менеджера" :error="errors.responsible_id" />
  </CreateModalWrapper>
</template>

<script setup lang="ts">
  import { onMounted, ref } from 'vue'
  import LeadIco from '../../icons/Kanban/LeadIco.vue';
  import ProductIco from '../../icons/Kanban/ProductIco.vue';
  import ClientIco from '../../icons/Kanban/ClientIco.vue';
  import SourceIco from '../../icons/Kanban/SourceIco.vue';
  import NextActionIco from '../../icons/Kanban/NextActionIco.vue';
  import CommentIco from '../../icons/Kanban/CommentIco.vue';
  import TextField from '../../CRM/modals/fields/TextField.vue';
  import SelectField from '../../CRM/modals/fields/SelectField.vue';
  import CreateModalWrapper from '../../CRM/modals/CreateModalWrapper.vue'
  import NumberField from '../../CRM/modals/fields/NumberField.vue';
  import StageField from '../../CRM/modals/fields/StageField.vue';
  import { LeadErrors, LeadRequest } from '../../../types/lead.ts';
  import { validateLead } from '../../../validators/lead.ts';
  import {useKanbanStore} from '../../../stores/kanban.ts';
  import { useCurrentClient } from '../../../composables/Clients/useCurrentClient.ts';
  import { usePipelineStore } from '../../../stores/pipelines.ts';
  import { useResponsiblesStore } from '../../../stores/responsibles.ts';

  const emit = defineEmits(['close']);
  
  const kanbanStore = useKanbanStore();
  const pipelineStore = usePipelineStore();
  const responsibleStore = useResponsiblesStore();
  const { client, newLead } = useCurrentClient();

  
  const accepting = ref(false);
  const generalError = ref<string | null>(null);
  const errors = ref<LeadErrors>({
    name: null,
    budget: null,
    product: null,
    source: null,
    next_action: null,
    comment: null,
    stage_id: null,
    client_id: null,
    new_client: null,
    responsible_id: null
  });
  
  const name = ref('');
  const budget = ref(0);
  const product = ref('');
  const source = ref('');
  const next_action = ref('');
  const comment = ref('');
  const selectedStageId = ref(null);
  const selectedPipelineId = ref(null);
  const currentManager = ref();

  async function submit() {
    const data: LeadRequest = {
      name: name.value,
      budget: budget.value,
      product: product.value,
      source: source.value,
      next_action: next_action.value,
      comment: comment.value,
      stage_id: selectedStageId.value,
      responsible_id: currentManager.value,
      client_id: client.value?.id
    };
    const validationErrors = validateLead(data, false);
    if (!validationErrors.isValid) {
      errors.value = validationErrors.errors;
      return false;
    }
    accepting.value = true;
    try {
      const lead = await kanbanStore.createLead(data);
      if(lead)
        newLead(lead);
    }
    catch {
      generalError.value = 'Не удалось создать сделку. Попробуйте чуть позже';
      return;
    }
    finally {
      accepting.value = false;
    }
    emit('close');
  }

  onMounted(async ()=>{
    try{
      await pipelineStore.loadPipelines();
      await responsibleStore.loadResponsibles();
    }
    catch {
      alert("Ошибка загрузки данных");
      emit("close");
    }
  });

</script>
<style>
.create-modal__btn {
  display: flex;
  justify-content: center;
}
.create-modal__btn button {
    width: 45%;
    height: 42px;

    border: 1px dashed #cfd6df;
    border-radius: 8px;

    background: #f8fafc;
    color: #374151;

    font-size: 14px;
    font-weight: 500;

    cursor: pointer;

    transition: all 0.15s ease;
}

.create-modal__btn button:hover {
    background: #f1f5f9;
    border-color: #9ca3af;
}

.create-modal__btn button:active {
    transform: scale(0.98);
}
</style>