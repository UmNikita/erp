<template>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Воронка</th>
                    <th>Этап</th>
                    <th>Сумма</th>
                    <th>Статус</th>
                    <th>Ответственный</th>
                    <th>Создана</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <LeadRow v-for="lead in props.leads" :lead="lead" />
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
    import { LeadResponse } from '../../../../../types/lead.ts';
    import LeadRow from './LeadRow.vue';

    const props = defineProps<{
        leads: LeadResponse[];
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