<template>
    <FieldWrapper :required="required" :title="title" :ico="ico" :error="error">
        <input v-model="model" type="color">
    </FieldWrapper>
</template>

<script setup lang="ts">
    import type { Component } from 'vue';
    import FieldWrapper from './FieldWrapper.vue';

    const model = defineModel<string>();
    const props = defineProps<{
        ico: Component;
        title: string;
        error?: string | null;
        required: boolean;
    }>();
</script>

<style scoped>
    .create-modal__input-wrap input
    {
        width: 100%;
        height: 45px;
        border: none;
        border-radius: 8px;
        background: #ffffff;
        cursor: pointer;
    }
</style>