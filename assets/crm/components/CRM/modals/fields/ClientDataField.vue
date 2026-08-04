<template>
    <FieldWrapper :required="false" title="Клиент" :ico="ClientIco" :error="error">
        <input v-model="name" type="text" placeholder="Введите имя клиента">
        <input v-model="email" type="text" placeholder="Введите email клиента">
        <input v-model="phone" type="text" placeholder="Введите phone клиента">
    </FieldWrapper>
</template>

<script setup lang="ts">
    import ClientIco from '../../../icons/Kanban/ClientIco.vue';
    import FieldWrapper from './FieldWrapper.vue';

    const name = defineModel<string>('name');
    const email = defineModel<string>('email');
    const phone = defineModel<string>('phone');

    const props = defineProps<{
        error?: string;
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