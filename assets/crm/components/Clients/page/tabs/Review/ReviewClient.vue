<template>
    <section>
        <div class="grid">
            <Board />
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
        </div>

        <div class="bottom">
            <article class="card">
                <div class="card-head">
                    <h2>Последние сделки</h2>
                    <button class="link open-panel" data-target="deals">Все сделки</button>
                </div>
                <div class="list">
                    <Lead v-for="lead in lastThree" :lead="lead" />
                </div>
            </article>

            <article class="card">
                <div class="card-head">
                    <h2>Последняя активность</h2>
                    <button class="link open-panel" data-target="history">Вся история</button>
                </div>
                <div class="activity">
                    <History v-for="record in lastHistory" :record="record" />
                </div>
            </article>
        </div>
    </section>
</template>

<script setup lang="ts">
    import { computed } from 'vue';
    import { formatAmount } from '../../../../../utils/fields';
    import Lead from './Lead.vue';
    import Board from './Board.vue';
    import { useCurrentClient } from '../../../../../composables/Clients/useCurrentClient.ts';
    import History from './History.vue';

    const { client, leads, history } = useCurrentClient();

    const lastThree = computed(() => leads.value.slice(-3));
    const lastHistory = computed(() => history.value.slice(-3));

</script>

<style scoped>
    .grid {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(310px, .65fr);
        gap: 18px;
    }
    .bottom {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(300px, .65fr);
        gap: 18px;
        margin-top: 18px;
    }
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

    .link {
        padding: 0;
        border: 0;
        background: transparent;
        color: var(--blue);
        font-size: 13px;
        font-weight: 600;
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
    
    .list {
        display: grid;
        gap: 10px;
    }

    .activity {
        display: grid;
        gap: 18px;
    }

</style>