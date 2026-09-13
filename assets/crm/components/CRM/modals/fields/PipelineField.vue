<template>
    <div class="pipeline-settings__item">
        <div class="pipeline-settings__icon">
            <PipelineIco />
        </div>

        <div class="pipeline-settings__content">
            <label class="pipeline-settings__label">Название воронки</label>

            <input
                v-model="props.pipeline.name"
                placeholder="Введите название воронки"
                :class="{'pipeline-settings__input--error' : error}"
                @input="changePipeline"
            />

            <p v-if="error" class="pipeline-settings__field-error">
                {{ error }}
            </p>
        </div>

        <button class="pipeline-settings__delete" @click="deletePipeline">
            <DeleteIco />
        </button>
    </div>
</template>

<script setup lang="ts">
    import { PipelineDetail } from '../../../../types/pipeline.ts';
    import DeleteIco from '../../../icons/DeleteIco.vue';
    import PipelineIco from '../../../icons/Kanban/PipelineIco.vue';
    import { ref, watch } from 'vue';

    const props = defineProps<{
      pipeline: PipelineDetail;
      validateError: string | null;
    }>();

    const error = ref<string | null>(null);

    watch(
        () => props.validateError,
        (newValue, oldValue) => {
            error.value = newValue;
        }
    );

    const emit = defineEmits(['delete', 'change'])

    function changePipeline() {
        emit("change", props.pipeline);
    }

    function deletePipeline() {
        if(props.pipeline.stages.length > 0) {
            error.value = "Невозможно удалить воронку с этапами";
            return;
        }
        emit("delete", props.pipeline);
    }
</script>

<style scoped>
    input {
        width: 100%;
        height: 45px;
        padding: 0 16px;
        border: 1px solid #dfe3e9;
        border-radius: 8px;
        outline: none;
        background: #ffffff;
        color: #171b24;
        font: inherit;
        font-size: 14px;
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

    .pipeline-settings__input--error {
        border-color: #e5484d;
        background: #fffafa;
    }

    @media (max-width: 600px) {
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
    }
</style>