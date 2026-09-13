<template>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Имя</th>
                    <th>Фамилия</th>
                    <th>Отчество</th>
                    <th>Должность</th>
                    <th>Телефон</th>
                    <th>Email</th>
                    <th>Мессенджер</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <ContactRow @edit="edit" v-for="contact in contacts" :contact="contact" />
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
    import { Contact } from '../../../../../types/contact';
    import ContactRow from './ContactRow.vue';

    const emit = defineEmits(['edit']);

    function edit(id: number) {
        emit("edit", id);
    }

    const props = defineProps<{
        contacts: Contact[];
    }>();

</script>

<style>
    .table-wrap {
        overflow: auto;
        border: 1px solid var(--line);
        border-radius: 12px;
        height: 800px;
    }

    .table {
        width: 100%;
        min-width: 1120px;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .table th {
        height: 50px;
        padding: 0 16px;
        background: #f8f9fb;
        border-bottom: 1px solid var(--line);
        color: var(--muted);
        font-size: 12px;
        text-align: left;
        text-transform: uppercase;
    }
</style>