<template>
    <section>
        <div class="section-head">
            <div>
                <h2>История клиента</h2>
                <p>Все изменения и действия сотрудников по клиенту</p>
            </div>
        </div>
        <div class="history-day">
            <!-- <h3>Сегодня</h3> -->
            <Record v-for="record in history" :record="record" />
        </div>
    </section>
</template>

<script setup lang="ts">
    import { useCurrentClient } from '../../../../../composables/Clients/useCurrentClient';
    import Record from './Record.vue';

    const { history } = useCurrentClient();
</script>

<style scoped>
    .section-head {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .section-head h2 {
        margin: 0 0 5px;
        font-size: 20px;
    }

    .section-head p {
        margin: 0;
        color: var(--muted);
        font-size: 13px;
    }

    .history-day {
        margin-bottom: 22px;
    }

    .history-day h3 {
        margin: 0 0 14px;
        font-size: 14px;
        color: #434b59;
    }
</style>