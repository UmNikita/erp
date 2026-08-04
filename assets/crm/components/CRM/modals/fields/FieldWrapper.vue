<template>
    <div class="create-modal__field">
        <div class="create-modal__label">
            <span class="create-modal__icon"><component  v-if="ico" :is="ico" /></span>
            <span>{{ title }}<span class="create-modal__req" v-if="required"> *</span></span>
        </div>
        <div class="create-modal__input-wrap">
            <slot />
            <span v-if="error" class="create-modal__error">{{ error }}</span>
        </div>
    </div>
</template>

<script setup lang="ts">
    import type { Component } from 'vue';

    const props = defineProps<{
        ico: Component;
        title: string;
        error?: string;
        required: boolean;
    }>();
</script>

<style>
    .create-modal__field {
        display: grid;
        grid-template-columns: 225px 1fr;
        align-items: center;
    }
    .create-modal__label {
        display: flex;
        align-items: center;
        gap: 20px;
        font-size: 15px;
        font-weight: 600;
    }
    .create-modal__input-wrap {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 7px;
    }
    .create-modal__error {
        display: flex;
        align-items: center;
        gap: 6px;

        color: #d93d42;
        font-size: 12px;
        line-height: 1.4;
        font-weight: 500;
    }

    .create-modal__req {
        color: #d93d42;
    }

    .create-modal__error::before {
        content: "!";
        width: 16px;
        height: 16px;

        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 16px;

        color: #ffffff;
        background: #e5484d;
        border-radius: 50%;

        font-size: 11px;
        font-weight: 700;
    }
    .create-modal__icon {
        width: 28px;
        color: #1267f4;
        font-size: 25px;
        font-weight: 400;
        text-align: center;
    }

    .create-modal input::placeholder {
        color: #80899d;
    }

    .create-modal input:focus,
    .create-modal select:focus {
        border-color: #1267f4;
        box-shadow: 0 0 0 3px rgba(18, 103, 244, 0.08);
    }
    @media (max-width: 700px) {
        .create-modal__field {
            grid-template-columns: 1fr;
            gap: 10px;
        }
    }
</style>