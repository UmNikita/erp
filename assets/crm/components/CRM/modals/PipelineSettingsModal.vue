<template>
  <div class="modal-overlay" @click.self="emit('close')">
      <div class="pipeline-settings">
        <div class="pipeline-settings__header">
          <div>
            <h2 class="pipeline-settings__title">Управление воронками</h2>
            <p class="pipeline-settings__description">
              Изменяйте названия или удаляйте ненужные воронки
            </p>
          </div>

          <button class="pipeline-settings__close" @click="emit('close')"><ExitModalIco /></button>
        </div>

        <div class="pipeline-settings__list">
          <div v-for="pipeline in localPipelines" :key="pipeline.id" class="pipeline-settings__item">
            <div class="pipeline-settings__icon">
              <PipelineIco />
            </div>

            <div class="pipeline-settings__content">
              <label class="pipeline-settings__label">Название воронки</label>

              <input
                v-model="pipeline.name"
                @input="changePipeline(pipeline)"
                class="pipeline-settings__input"
                :class="{
                  'pipeline-settings__input--error':
                    errors[pipeline.id]
                }"
                type="text"
                placeholder="Введите название воронки"
              >

              <p
                v-if="errors[pipeline.id]"
                class="pipeline-settings__field-error"
              >
                {{ errors[pipeline.id] }}
              </p>
            </div>

            <button
              class="pipeline-settings__delete"
              type="button"
              title="Удалить воронку"
              @click="deletePipeline(pipeline, pipeline.stages.length > 0)"
            >
              <DeleteIco />
            </button>
          </div>
        </div>

        <p v-if="props.error" class="pipeline-settings__general-error">{{ props.error }}</p>

        <div class="pipeline-settings__footer">
          <button
            class="pipeline-settings__button pipeline-settings__button--cancel"
            type="button"
            @click="emit('close')"
          >
            Отмена
          </button>

          <button
            class="pipeline-settings__button pipeline-settings__button--save"
            type="button"
            @click="savePipelines"
          >
            Сохранить изменения
          </button>
        </div>
      </div>
  </div>
</template>

<script setup lang="ts">
  import { onMounted, ref } from 'vue'
  import { PipelineDetail, PipelineBuffersDTO } from '../../../types/pipeline';
  import PipelineIco from '../../icons/Kanban/PipelineIco.vue';
  import DeleteIco from '../../icons/DeleteIco.vue';
  import ExitModalIco from '../../icons/ExitModalIco.vue';
  import { pipelineDetailToPipelineList } from '../../../mappers/pipelineMapper.ts';

  const emit = defineEmits(['close', 'submit'])

  const props = defineProps<{pipelines: PipelineDetail[], error: string | null}>()

  const errors = ref<Record<string, string>>({});
  const localPipelines = ref<PipelineDetail[]>([]);
  const pipelinesDeleteBuffer = ref<PipelineDetail[]>([]);
  const pipelinesChangeBuffer = ref<Map<number, PipelineDetail>>(new Map());

  onMounted(() => {
      localPipelines.value = JSON.parse(JSON.stringify(props.pipelines));
  });

  function savePipelines() {
    const updatePipelines = Array.from(pipelinesChangeBuffer.value.values());
    const data: PipelineBuffersDTO = {
        update: pipelineDetailToPipelineList(updatePipelines),
        delete: pipelineDetailToPipelineList(pipelinesDeleteBuffer.value)
    };

    emit('submit', data);
  }

  function deletePipeline(pipeline: PipelineDetail, hasStages: boolean) {
    if(hasStages) {
      errors.value[pipeline.id] = "Невозможно удалить воронку с этапами";
      return;
    }
    localPipelines.value = localPipelines.value.filter(item => item.id !== pipeline.id);
    pipelinesChangeBuffer.value.delete(pipeline.id);
    pipelinesDeleteBuffer.value.push(pipeline);
  }

  function changePipeline(pipeline: PipelineDetail) {
    const original = props.pipelines.find(item => item.id === pipeline.id);
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

.pipeline-settings__item {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 16px;
  background: #fafbfc;
  border: 1px solid #e3e7ed;
  border-radius: 12px;
}

.pipeline-settings__icon {
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #1267f4;
  background: #edf4ff;
  border-radius: 10px;
}

.pipeline-settings__icon svg {
  width: 23px;
  height: 23px;
}

.pipeline-settings__content {
  min-width: 0;
  flex: 1;
}

.pipeline-settings__label {
  display: block;
  margin-bottom: 7px;
  color: #707b8e;
  font-size: 12px;
  font-weight: 500;
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

.pipeline-settings__input--error {
  border-color: #e5484d;
  background: #fffafa;
}

.pipeline-settings__field-error {
  margin: 6px 0 0;
  color: #d93d42;
  font-size: 12px;
  line-height: 1.4;
}

.pipeline-settings__delete {
  width: 42px;
  height: 42px;
  flex: 0 0 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 19px;
  padding: 0;
  color: #9a6266;
  background: #fff4f4;
  border: 1px solid #f2d9db;
  border-radius: 9px;
  cursor: pointer;
  transition: 0.2s ease;
}

.pipeline-settings__delete:hover {
  color: #ffffff;
  background: #e5484d;
  border-color: #e5484d;
}

.pipeline-settings__delete svg {
  width: 20px;
  height: 20px;
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

  .pipeline-settings__item {
    gap: 10px;
    padding: 13px;
  }

  .pipeline-settings__icon {
    display: none;
  }

  .pipeline-settings__delete {
    margin-top: 19px;
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