<template>
  <CreateModalWrapper :accepting="accepting" title-btn="Создать" title="Создание воронки" subtitle="Заполните информацию о воронке"
  :error="generalError" @submit="accept" @close="emit('close')">
    <TextField :required="true" :ico="PipelineIco" title="Название воронки" v-model="name" 
    placeholder="Введите название воронки" :error="errors.name" />
  </CreateModalWrapper>
</template>

<script setup lang="ts">
  import PipelineIco from '../../icons/Kanban/PipelineIco.vue';
  import TextField from './fields/TextField.vue';
  import CreateModalWrapper from './CreateModalWrapper.vue'
  import { usePipelineStore } from '../../../stores/pipelines.ts';
  import { ref } from 'vue';
  import { isMaxPipeline, validatePipeline } from '../../../validators/pipeline.ts';
  import { PipelineErrors } from '../../../types/pipeline.ts';

  const emit = defineEmits(['close']);

  const name = ref('');
  const accepting = ref(false);
  const pipelineStore = usePipelineStore()
  const generalError = ref<string | null>(null);
  const errors = ref<PipelineErrors>({name: ''});

  async function accept() {
    if(isMaxPipeline(pipelineStore.pipelines)) {
      generalError.value = 'Достигнуто максимальное кол-во воронок!';
      return;
    }
    const validationErrors = validatePipeline(name.value);
    if (!validationErrors.isValid) {
      errors.value = validationErrors.errors;
      return false;
    }
    accepting.value = true;
    try {
      await pipelineStore.createPipeline(name.value);
    }
    catch {
      generalError.value = 'Не удалось создать воронку. Попробуйте чуть позже';
      return;
    }
    finally {
      accepting.value = false;
    }
    emit('close');
  }
</script>