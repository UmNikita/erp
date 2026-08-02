<template>
  <CreateModalWrapper title="Создание сделки" subtitle="Заполните информацию о сделки"
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
  </CreateModalWrapper>
</template>

<script setup lang="ts">
  import { ref } from 'vue'
  import LeadIco from '../../icons/LeadIco.vue';
  import ProductIco from '../../icons/Kanban/ProductIco.vue';
  import SourceIco from '../../icons/Kanban/SourceIco.vue';
  import NextActionIco from '../../icons/Kanban/NextActionIco.vue';
  import CommentIco from '../../icons/Kanban/CommentIco.vue';
  import TextField from './fields/TextField.vue';
  import CreateModalWrapper from './CreateModalWrapper.vue'
  import NumberField from './fields/NumberField.vue';
  import StageField from './fields/StageField.vue';
  import { PipelineDetail } from '../../../types/pipeline.ts';
  import { validateLead } from '../../../validators/lead.ts';

  const emit = defineEmits(['close', 'submit'])

  const name = ref('');
  const budget = ref(0);
  const product = ref('');
  const source = ref('');
  const next_action = ref('');
  const comment = ref('');
  const selectedStageId = ref();
  const errors = ref<Record<string, string>>({});
  const props = defineProps<{ 
    error?: string | null,
    pipelinesDetail: PipelineDetail[]
  }>();

  function close() {
    emit('close')
  }

  function submit() {
    errors.value = {};
    const data = {
      name: name.value,
      budget: budget.value,
      product: product.value,
      source: source.value,
      next_action: next_action.value,
      comment: comment.value,
      stage_id: selectedStageId.value,
    };
    const validationErrors = validateLead(data);
    if (!validationErrors.isValid) {
        errors.value = validationErrors.errors;
        return;
    }
    emit('submit', data);
  }
</script>