<template>
    <div class="create-modal__field-select">
        <div class="create-modal__select-wrap">
            <select v-model="selectedPipelineId">
                <option :value="null" disabled>Выберите воронку</option>
                <option v-for="pipeline in pipelinesDetail" :key="pipeline.id" :value="pipeline.id" >{{ pipeline.name }}</option>
            </select>
        </div>
        <div class="create-modal__select-wrap">
            <select v-model="selectedStageId" :disabled="!selectedPipelineId">
                <option :value="null" disabled>Выберите этап</option>
                <option v-for="stage in stages"
                :key="stage.id" :value="stage.id">{{ stage.name }}</option>
            </select>
        </div>
        <div class="create-modal__input-wrap">
            <slot />
            <span v-if="error" class="create-modal__error">{{ error }}</span>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { computed, ref, watch } from 'vue';
    import { PipelineDetail } from '../../../../types/pipeline';

    const props = defineProps<{
        title: string;
        error?: string;
        pipelinesDetail: PipelineDetail[];
    }>();

    const selectedStageId = defineModel<number | null>('selectedStageId');
    const selectedPipelineId = defineModel<number | null>('selectedPipelineId');

    const stages = computed(() => {
        return props.pipelinesDetail.find(p => p.id === selectedPipelineId.value)?.stages ?? [];
    });


    watch(selectedPipelineId, (newValue, oldValue) => {
        if (oldValue !== undefined && oldValue !== null) {
            selectedStageId.value = null;
        }
    });
</script>

<style>
    .create-modal__select-wrap select
    {
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
        margin-top: 25px;
    }
    .create-modal__field-select {
        display: grid;
        grid-template-columns: 0.7fr 0.7fr;
        gap: 15px;
        align-items: center;
    }
</style>