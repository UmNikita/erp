<template>
    <FieldWrapper :required="required" :title="title" :ico="ico" :error="error">
        <select v-model="model">
            <option :value="null" disabled>{{firstElement}}</option>
            <option v-for="element in elements"
            :key="element.id" :value="element.id">{{ element.name }}</option>
        </select>
    </FieldWrapper>
</template>

<script setup lang="ts">
    import type { Component } from 'vue';
    import FieldWrapper from './FieldWrapper.vue';
    import { Responsible } from '../../../../types/kanban.ts';

    const model = defineModel<number | null>({default: null});
    const props = defineProps<{
        ico: Component;
        title: string;
        firstElement: string;
        error?: string;
        required: boolean;
        elements: Responsible[];
    }>();
</script>

<style>
    .create-modal__input-wrap select
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
    }
</style>