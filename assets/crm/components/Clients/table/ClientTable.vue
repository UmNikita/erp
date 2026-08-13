<template>
    <div class="table-wrap">
        <table v-if="clients.length > 0" class="table">
            <colgroup>
                <col style="width:30%">
                <col style="width:25%">
                <col style="width:15%">
                <col style="width:20%">
                <col style="width:10%">
            </colgroup>
            <thead>
                <tr>
                    <th>Клиент</th>
                    <th>Контакты компании</th>
                    <th>Сделки</th>
                    <th>Последняя активность</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <ClientRow @delete="deleteClient" v-for="client in clients" :client="client" />
            </tbody>
        </table>
        <ClientEmpty v-else />
    </div>
</template>

<script setup lang="ts">
    import { Client } from '../../../types/client.ts';
    import ClientEmpty from './ClientEmpty.vue';
    import ClientRow from './ClientRow.vue';

    const emit = defineEmits(['delete']);

    function deleteClient(data: Client) {
        emit("delete", data);
    }

    const props = defineProps<{
        clients: Client[];
    }>();

</script>

<style>
    .table-wrap {
        overflow: auto;
        border: 1px solid var(--line);
        border-radius: 12px;
        min-height: 800px;
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