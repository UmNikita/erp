<template>
    <article class="card">
        <div class="card-head">
            <h2>Показатели</h2>
        </div>
        <div class="stats">
            <div class="stat">
                <span>Сделок</span>
                <strong>{{ client?.count_leads }}</strong>
                <small>На сумму {{ formatAmount(client?.amount_sum_leads) }} ₽</small>
            </div>
            <div class="stat">
                <span>LTV</span>
                <strong>{{ formatAmount(client?.ltv) }} ₽</strong>
                <small>Средний чек сделки: {{ formatAmount(client?.average_cheque) }} ₽</small>
            </div>
            <div class="stat">
                <span>Последний контакт</span>
                <strong>Сегодня</strong>
                <small>в 15:40</small>
            </div>
            <div class="stat">
                <span>Количечтво контактов</span>
                <strong>{{ client?.contacts.length }}</strong>
            </div>
        </div>
    </article>
</template>

<script setup lang="ts">
    import { formatAmount } from '../../../../../utils/fields';
    import { useCurrentClient } from '../../../../../composables/Clients/useCurrentClient.ts';

    const { client } = useCurrentClient();
</script>

<style scoped>
    .card {
        padding: 22px;
        border: 1px solid var(--line);
        border-radius: 13px;
        background: #fff;
    }

    .card-head {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }

    .card h2 {
        margin: 0;
        font-size: 17px;
    }

    .stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .stat {
        min-height: 102px;
        padding: 16px;
        border: 1px solid #edf0f4;
        border-radius: 11px;
        background: #f8f9fb;
    }

    .stat span {
        display: block;
        margin-bottom: 12px;
        color: var(--muted);
        font-size: 12px;
    }

    .stat strong {
        display: block;
        font-size: 21px;
    }

    .stat small {
        display: block;
        margin-top: 5px;
        color: var(--muted);
    }

</style>