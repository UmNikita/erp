<template v-if="loading">
    <section v-if="!error">
        <div class="grid">
            <Board />
            <Statistic />
        </div>

        <div class="bottom">
            <article class="card">
                <div class="card-head">
                    <h2>Последние сделки</h2>
                    <router-link class="link open-panel" :to="clientLeadsUrl(clientId)">Все сделки</router-link>
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
    <Error v-else />
</template>

<script setup lang="ts">
    import { computed, onMounted, ref } from 'vue';
    import Lead from './Lead.vue';
    import Board from './Board.vue';
    import { useCurrentClient } from '../../../../../composables/Clients/useCurrentClient.ts';
    import History from './History.vue';
    import Error from '../../../../Error.vue';
    import { useRoute } from 'vue-router';
    import Statistic from './Statistic.vue';
    import { clientLeadsUrl } from '../../../../../routes/client.ts';

    const { leads, history, loadReview } = useCurrentClient();

    const lastThree = computed(() => leads.value.slice(-3));
    const lastHistory = computed(() => history.value.slice(0, 3));

    const error = ref(false);
    const loading = ref(false);

    const route = useRoute();
    const clientId = Number(route.params.id);

    onMounted(async ()=>{
        try{
            await loadReview(clientId);
        }
        catch {
            error.value = true;
        }
        finally {
            loading.value = true;
        }
    });

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

    .list {
        display: grid;
        gap: 10px;
    }

    .activity {
        display: grid;
        gap: 18px;
    }

</style>