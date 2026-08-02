<template>
  <CreateModalWrapper title="Создание воронки" subtitle="Заполните информацию о воронке"
  :error="error" @submit="submit" @close="close">
    <TextField :required="true" :ico="PipelineIco" title="Название воронки" v-model="name" 
    placeholder="Введите название воронки" :error="errors.name" />
  </CreateModalWrapper>
</template>

<script setup lang="ts">
  import { ref } from 'vue';
  import PipelineIco from '../../icons/PipelineIco.vue';
  import { validatePipeline } from '../../../validators/pipeline.ts';
  import TextField from './fields/TextField.vue';
  import CreateModalWrapper from './CreateModalWrapper.vue'

  const emit = defineEmits(['close', 'submit']);

  const name = ref('');
  const errors = ref<Record<string, string>>({});

  const props = defineProps<{ error?: string | null }>();

  function close() {
    emit('close');
  }

  function submit() {
    errors.value = {};
    const data = {name: name.value};
    const validationErrors = validatePipeline(data);
    if (!validationErrors.isValid) {
        errors.value = validationErrors.errors;
        return;
    }
    emit('submit', data);
  }
</script>