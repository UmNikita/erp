<template>
  <CreateModalWrapper title="Создание этапа" subtitle="Заполните информацию об этапе"
  :error="error" @submit="submit" @close="close">
    <TextField :required="true" :ico="StageIco" title="Название этапа" v-model="name" 
    placeholder="Введите название этапа" :error="errors.name" />
    <ColorField :required="true" :ico="ColorIco" title="Цвет этапа" v-model="color" 
    placeholder="Введите цвет этапа на доске" :error="errors.color" />
  </CreateModalWrapper>
</template>

<script setup lang="ts">
  import { ref } from 'vue';
  import { validateStage } from '../../../validators/stage.ts';
  import StageIco from '../../icons/StageIco.vue';
  import ColorIco from '../../icons/ColorIco.vue';
  import TextField from './fields/TextField.vue';
  import ColorField from './fields/ColorField.vue';
  import CreateModalWrapper from './CreateModalWrapper.vue';

  const emit = defineEmits(['close', 'submit']);

  const name = ref('');
  const color = ref('');
  const errors = ref<Record<string, string>>({});

  const props = defineProps<{ error?: string | null }>();

  function close() {
    emit('close');
  }

  function submit() {
    errors.value = {};
    const data = {name: name.value, color: color.value};
    const validationErrors = validateStage(data);
    if (!validationErrors.isValid) {
        errors.value = validationErrors.errors;
        return;
    }
    emit('submit', data);
  }
</script>