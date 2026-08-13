<template>
  <CreateModalWrapper title-btn="Создать" title="Создание этапа" subtitle="Заполните информацию об этапе"
  :error="error" @submit="submit" @close="close">
    <TextField :required="true" :ico="StageIco" title="Название этапа" v-model="name" 
    placeholder="Введите название этапа" :error="errors.name" />
    <ColorField :required="true" :ico="ColorIco" title="Цвет этапа" v-model="color" 
    placeholder="Введите цвет этапа на доске" :error="errors.color" />
  </CreateModalWrapper>
</template>

<script setup lang="ts">
  import { ref } from 'vue';
  import StageIco from '../../icons/Kanban/StageIco.vue';
  import ColorIco from '../../icons/Kanban/ColorIco.vue';
  import TextField from './fields/TextField.vue';
  import ColorField from './fields/ColorField.vue';
  import CreateModalWrapper from './CreateModalWrapper.vue';

  const emit = defineEmits(['close', 'submit']);

  const name = ref('');
  const color = ref('');

  const props = defineProps<{ 
    error?: string | null,
    errors: Record<string, string>
   }>();

  function close() {
    emit('close');
  }

  function submit() {
    const data = {name: name.value, color: color.value};
    emit('submit', data);
  }
</script>