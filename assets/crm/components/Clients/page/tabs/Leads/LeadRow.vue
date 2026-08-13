<template>
    <tr>
        <td>
            <strong>{{ lead.name }}</strong>
        </td>
        <td>{{ lead.stage.pipeline.name }}</td>
        <td>{{ lead.stage.name }}</td>
        <td>{{ formatAmount(lead.budget) }} ₽</td>
        <td class="stage">{{ formatStatus(lead.status) }}</td>
        <td>{{ lead.responsible?.name }}</td>
        <td>{{ formatResponseDate(lead.dateStart) }}</td>
        <td>
            <router-link class="icon-btn" to="/">
                <svg viewBox="0 0 24 24" fill="none"><path d="m9 5 7 7-7 7" stroke="currentColor" stroke-width="1.8"/></svg>
            </router-link>
        </td>   
    </tr>
</template>

<script setup lang="ts">
    import { LeadResponse } from '../../../../../types/lead';
    import { formatAmount, formatResponseDate, formatStatus } from '../../../../../utils/fields';

    const props = defineProps<{
        lead: LeadResponse;
    }>();

</script>

<style scoped>
    .table td {
        padding: 15px;
        border-bottom: 1px solid #edf0f4;
        font-size: 13px;
    }

    .table tr {
        border-bottom: 1px solid #edf0f4;
    }

    .table tr:last-child td {
        border-bottom: 0;
    }

    .stage {
        display: inline-flex;
        align-items: center;
        height: 27px;
        padding: 0 9px;
        border-radius: 999px;
        color: #f97316;
        margin-top: 16px;
        font-weight: 650;
    }

    .icon-btn {
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
        border: 1px solid #edf0f4;
        border-radius: 9px;
        color: #5e697c;
        cursor: pointer;
    }

</style>