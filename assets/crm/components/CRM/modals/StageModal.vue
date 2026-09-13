<template>
  <CreateModalWrapper :accepting="accepting" title-btn="Создать" title="Создание этапа" subtitle="Заполните информацию об этапе"
  :error="generalError" @submit="accept" @close="emit('close');">
    <TextField :required="true" :ico="StageIco" title="Название этапа" v-model="name" 
    placeholder="Введите название этапа" :error="errors.name" />
    <ColorField :required="true" :ico="ColorIco" title="Цвет этапа" 
    v-model="color" :error="errors.color" />
  </CreateModalWrapper>
</template>

<script setup lang="ts">
  import { ref } from 'vue';
  import StageIco from '../../icons/Kanban/StageIco.vue';
  import ColorIco from '../../icons/Kanban/ColorIco.vue';
  import TextField from './fields/TextField.vue';
  import ColorField from './fields/ColorField.vue';
  import CreateModalWrapper from './CreateModalWrapper.vue';
  import { getStageRequest } from '../../../mappers/stageMapper.ts';
  import { StageErrors } from '../../../types/stage.ts';
  import { isMaxStages, validateStage } from '../../../validators/stage.ts';
  import { useKanbanStore } from '../../../stores/kanban.ts';

  const emit = defineEmits(['close']);

  const name = ref('');
  const accepting = ref(false);
  const color = ref('#3772a9');

  const errors = ref<StageErrors>({name: null, color: null});
  const generalError = ref<string | null>(null);
  const kanbanStore = useKanbanStore();

  const props = defineProps<{ 
    pipelineId: number;
  }>();

  async function accept() {
    if(kanbanStore.kanban) {
      if(isMaxStages(kanbanStore.kanban.stages)) {
        generalError.value = 'Достигнуто максимальное кол-во этапов!';
        return;
      }
      const data = getStageRequest(name.value, color.value, props.pipelineId);
      const validationErrors = validateStage(data);
      if (!validationErrors.isValid) {
        errors.value = validationErrors.errors;
        return false;
      }
      accepting.value = true;
      try {
        
        await kanbanStore.createStage(data);
      }
      catch {
        generalError.value = 'Не удалось создать этап. Попробуйте чуть позже!';
        return;
      }
      finally {
        accepting.value = false;
      }
      emit('close');
    }
    
  }
</script>