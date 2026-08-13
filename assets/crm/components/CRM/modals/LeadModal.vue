<template>
  <CreateModalWrapper title-btn="Создать" title="Создание сделки" subtitle="Заполните информацию о сделки"
  :error="error" @submit="submit" @close="close">
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
    <StageField title="Комментарий" v-model="comment" :pipelines-detail="pipelinesDetail" 
    v-model:selected-stage-id="selectedStageId" :error="errors.stage_id" />
    <div v-if="!isNewClient" class="create-modal__btn">
      <button @click="setNewClient">Создать клиента</button>
    </div>
    <ClientSearchField v-if="!isNewClient" :error="errors.client_id" @select="setClient" />
    <ClientDataField v-if="isNewClient" v-model:name="newClientName" 
    v-model:email="newClientEmail" v-model:phone="newClientPhone" :error="errors.new_client" />
    <SelectField :required="false" :ico="ClientIco" title="Менеджер" v-model="currentManager" 
    :elements="responsibles" firstElement="Выбирите менеджера" :error="errors.comment" />
  </CreateModalWrapper>
</template>

<script setup lang="ts">
  import { ref } from 'vue'
  import LeadIco from '../../icons/Kanban/LeadIco.vue';
  import ProductIco from '../../icons/Kanban/ProductIco.vue';
  import ClientIco from '../../icons/Kanban/ClientIco.vue';
  import SourceIco from '../../icons/Kanban/SourceIco.vue';
  import NextActionIco from '../../icons/Kanban/NextActionIco.vue';
  import CommentIco from '../../icons/Kanban/CommentIco.vue';
  import TextField from './fields/TextField.vue';
  import SelectField from './fields/SelectField.vue';
  import CreateModalWrapper from './CreateModalWrapper.vue'
  import NumberField from './fields/NumberField.vue';
  import StageField from './fields/StageField.vue';
  import { PipelineDetail } from '../../../types/pipeline.ts';
  import ClientSearchField from './fields/ClientSearchField.vue';
  import { Client } from '../../../types/client.ts';
  import ClientDataField from './fields/ClientDataField.vue';
  import { Responsible } from '../../../types/kanban.ts';

  const emit = defineEmits(['close', 'submit'])

  const name = ref('');
  const budget = ref(0);
  const product = ref('');
  const source = ref('');
  const next_action = ref('');
  const comment = ref('');
  const selectedStageId = ref();
  const currentClient = ref();
  const newClientName = ref();
  const newClientEmail = ref();
  const newClientPhone = ref();
  const currentManager = ref();
  const isNewClient = ref(false);
  const props = defineProps<{ 
    error?: string | null,
    pipelinesDetail: PipelineDetail[],
    responsibles: Responsible[],
    errors: Record<string, string>
  }>();

  function setClient(client: Client) {
    currentClient.value = client.id;
  }

  function setNewClient() {
    isNewClient.value = true;
    currentClient.value = null;
  }

  function close() {
    emit('close')
  }

  function submit() {
    let newClient;
    if(newClientName.value || newClientPhone.value || newClientEmail.value) {
      newClient = {
        name: newClientName.value,
        email: newClientEmail.value,
        phone: newClientPhone.value 
      }
    }
    const data = {
      name: name.value,
      budget: budget.value,
      product: product.value,
      source: source.value,
      next_action: next_action.value,
      comment: comment.value,
      stage_id: selectedStageId.value,
      client_id: currentClient.value,
      client: newClient,
      responsible_id: currentManager.value
    };
    emit('submit', data, isNewClient.value);
  }
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