<template>
  <CreateModalWrapper :accepting="accepting" title-btn="Сохранить" title="Управление воронками" subtitle="Изменяйте названия или удаляйте ненужные воронки"
  :error="generalError" @submit="savePipelines" @close="emit('close')">
    <div class="pipeline-settings__list">
      <PipelineField @delete="deletePipeline" @change="changePipeline" :validate-error="pipeline.err" :pipeline="pipeline.record" v-for="pipeline in localPipelines" />
    </div>  
  </CreateModalWrapper>
</template>

<script setup lang="ts">
  import { onMounted, ref } from 'vue'
  import { PipelineDetail, PipelineBuffersDTO } from '../../../types/pipeline';
  import { usePipelineStore } from '../../../stores/pipelines.ts';
  import CreateModalWrapper from './CreateModalWrapper.vue';
  import PipelineField from './fields/PipelineField.vue';
  import { validatePipeline } from '../../../validators/pipeline.ts';

  const emit = defineEmits(['close']);
  const accepting = ref(false);
  const generalError = ref<string | null>(null);

  const localPipelines = ref<{record: PipelineDetail, err: string | null}[]>([]);

  const pipelinesDeleteBuffer = ref<PipelineDetail[]>([]);
  const pipelinesChangeBuffer = ref<Map<number, PipelineDetail>>(new Map());

  const pipelineStore = usePipelineStore();

  onMounted(() => {
    const values: PipelineDetail[] = JSON.parse(JSON.stringify(pipelineStore.pipelinesDetail));
    values.forEach(element => {
      localPipelines.value.push({record: element, err: null});
    });
  });

  async function savePipelines() {
    let isValid: boolean = true;
    localPipelines.value.forEach(element => {
      const res = validatePipeline(element.record.name);
      element.err = null;
      if(!res.isValid) {
        element.err = res.errors.name;
        isValid = false;
      }
    });
    if(!isValid)
      return;
    const updatePipelines = Array.from(pipelinesChangeBuffer.value.values());
    const data: PipelineBuffersDTO = {
      update: updatePipelines,
      delete: pipelinesDeleteBuffer.value
    };
    accepting.value = true;
    try {
      await commitRequests(data);
    }
    catch {
      generalError.value = 'Не удалось обновить воронки. Попробуйте чуть позже';
      return null;
    }
    finally {
      accepting.value = false;
    }
    emit("close");
  }

  function deletePipeline(pipeline: PipelineDetail) {
    localPipelines.value = localPipelines.value.filter(item => item.record.id !== pipeline.id);
    pipelinesChangeBuffer.value.delete(pipeline.id);
    pipelinesDeleteBuffer.value.push(pipeline);
  }

  function changePipeline(pipeline: PipelineDetail) {
    const original = pipelineStore.pipelinesDetail.find(item => item.id === pipeline.id);
    if (!original)
      return;
    if (original.name === pipeline.name) {
      pipelinesChangeBuffer.value.delete(pipeline.id);
      return;
    }
    pipelinesChangeBuffer.value.set(pipeline.id,
      {
        id: pipeline.id,
        name: pipeline.name,
        stages: pipeline.stages
      }
    );
  }
  
  async function commitRequests(data: PipelineBuffersDTO) {
    await pipelineStore.deleteArrayPipelines(data.delete);
    for (const pipeline of data.update) {
      await pipelineStore.updatePipeline(pipeline, pipeline.id);
    }
  }
</script>

<style scoped>
.pipeline-settings {
  width: 100%;
  max-width: 620px;
  padding: 28px;
  background: #ffffff;
  border-radius: 16px;
}

.pipeline-settings__header {
  display: flex;
  justify-content: space-between;
  gap: 20px;
  margin-bottom: 26px;
}

.pipeline-settings__title {
  margin: 0 0 7px;
  color: #171b24;
  font-size: 23px;
  line-height: 1.25;
}

.pipeline-settings__description {
  margin: 0;
  color: #788296;
  font-size: 14px;
  line-height: 1.5;
}

.pipeline-settings__close {
  width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #687386;
  background: #f5f7f9;
  border: none;
  border-radius: 10px;
  cursor: pointer;
}

.pipeline-settings__close:hover {
  color: #171b24;
  background: #eceff3;
}

.pipeline-settings__close svg {
  width: 20px;
  height: 20px;
}

.pipeline-settings__list {
  max-height: 390px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding-right: 4px;
  overflow-y: auto;
}

.pipeline-settings__input {
  width: 100%;
  height: 42px;
  padding: 0 13px;
  color: #171b24;
  background: #ffffff;
  border: 1px solid #dce1e8;
  border-radius: 8px;
  outline: none;
  font: inherit;
  font-size: 14px;
  transition: 0.2s ease;
}

.pipeline-settings__input:focus {
  border-color: #1267f4;
  box-shadow: 0 0 0 3px rgba(18, 103, 244, 0.08);
}

.pipeline-settings__general-error {
  position: relative;
  margin: 14px 0 0;
  padding: 10px 13px 10px 38px;
  color: #c9363e;
  background: #fff5f5;
  border: 1px solid #f2c9cc;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 500;
  line-height: 1.45;
}

.pipeline-settings__general-error::before {
  content: "!";
  position: absolute;
  top: 50%;
  left: 13px;
  width: 16px;
  height: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  transform: translateY(-50%);
  color: #ffffff;
  background: #e5484d;
  border-radius: 50%;
  font-size: 11px;
  font-weight: 700;
}

.pipeline-settings__footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 26px;
  padding-top: 20px;
  border-top: 1px solid #e7eaf0;
}

.pipeline-settings__button {
  height: 44px;
  padding: 0 20px;
  border-radius: 9px;
  font: inherit;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s ease;
}

.pipeline-settings__button--cancel {
  color: #343a46;
  background: #ffffff;
  border: 1px solid #dce1e8;
}

.pipeline-settings__button--cancel:hover {
  background: #f7f8fa;
}

.pipeline-settings__button--save {
  color: #ffffff;
  background: #1267f4;
  border: 1px solid #1267f4;
  box-shadow: 0 6px 14px rgba(18, 103, 244, 0.2);
}

.pipeline-settings__button--save:hover {
  background: #0959dc;
  border-color: #0959dc;
  transform: translateY(-1px);
}

@media (max-width: 600px) {
  .pipeline-settings {
    padding: 20px 16px;
    border-radius: 14px;
  }

  .pipeline-settings__footer {
    flex-direction: column-reverse;
  }

  .pipeline-settings__button {
    width: 100%;
  }
}

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);

  display: flex;
  justify-content: center;
  align-items: center;
}
</style>