<template>
  <CreateModalWrapper title="Создание воронки" subtitle="Заполните информацию о воронке"
  :error="error" @submit="emit('submit')" @close="emit('close')">
    <TextField :required="true" :ico="PipelineIco" title="Название воронки" v-model="name" 
    placeholder="Введите название воронки" :error="errors.name" />
  </CreateModalWrapper>
</template>

<script setup lang="ts">
  import PipelineIco from '../../icons/Kanban/PipelineIco.vue';
  import TextField from './fields/TextField.vue';
  import CreateModalWrapper from './CreateModalWrapper.vue'

  const emit = defineEmits(['close', 'submit']);

  const name = defineModel<string>();

  const props = defineProps<{
    error?: string | null,
    errors: Record<string, string>
  }>();
</script>