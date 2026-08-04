<template>
    <FieldWrapper :required="required" :title="title" :ico="ico" :error="error">
        <input v-model="model" type="number">
    </FieldWrapper>
</template>

<script setup lang="ts">
    import type { Component } from 'vue';
    import FieldWrapper from './FieldWrapper.vue';

    const model = defineModel<number>();
    const props = defineProps<{
        ico: Component;
        title: string;
        error?: string;
        required: boolean;
    }>();
</script>

<style>
    .create-modal__input-wrap input
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