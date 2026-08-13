<template>
    <div v-if="loading" class="archive-page">
        <div v-if="error"><Error /></div>
        <template v-else>
            <Header />
            <div class="archive-page__table-wrap">
                <table class="archive-page__table">
                    <thead>
                        <tr>
                            <th>Сделка</th>
                            <th>Клиент</th>
                            <th>Воронка</th>
                            <th>Сумма</th>
                            <th>Менеджер</th>
                            <th>Результат</th>
                            <th>Дата архивации</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <Row @delete="deleteLead" v-for="lead in leads.leads" :lead="lead" />
                    </tbody>
                </table>
            </div>

            <div class="archive-page__footer">
                <span>Показано {{ total }} из {{ allCount }} сделок</span>

                <div class="archive-page__pagination">
                    <router-link v-if="!isStart" class="page-btn" :to="archiveTableUrl(page-1)"><</router-link>
                    <div v-else class="page-btn deactive"><</div>
                    <router-link class="page-btn" :class="{ active: page === num }" v-for="num in range" :to="archiveTableUrl(num)">{{ num }}</router-link>
                    <router-link v-if="!isEnd" class="page-btn" :to="archiveTableUrl(page+1)">></router-link>
                    <div v-else class="page-btn deactive">></div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup lang="ts">
    import { computed, ref, watch } from 'vue';
    import { getArchiveLeads, deleteLead as deleteLeadApi } from '../../api/lead';
    import Header from './Header.vue';
    import Row from './Row.vue';
    import { LeadResponse } from '../../types/lead.ts';
    import { useRoute, useRouter } from 'vue-router';
    import { usePagination } from '../../composables/usePagination.ts';
    import Error from '../Error.vue';
    import { archiveTableUrl } from '../../routes/lead.ts';

    const LIMIT = 10;

    const leads = ref();
    const loading = ref(false);
    const error = ref(false);
    
    const total = ref(1);
    const route = useRoute();
    const router = useRouter();
    const page = computed(() => Number(route.query.page ?? 1));

    const {allCount, isStart, isEnd, range, setStates} = usePagination(page);

    async function setLeads(page: number) {
        try{
            leads.value = await getArchiveLeads();
            total.value = leads.value.pagination.count;
            if(page > 1)
                total.value += page * LIMIT;

            setStates(leads.value.pagination.allCount);
        }
        catch {
            error.value = true;
        }
        finally {
            loading.value = true;
        }
    }

    async function deleteLead(value: LeadResponse) {
        try {
            await deleteLeadApi(value.id);
            router.go(0);
        } catch {
            alert("Возникла ошибка!");
        }   
    }

    watch(
        page,
        async (newPage) => {
            await setLeads(newPage);
        },
        { immediate: true }
    );

</script>

<style scoped>
    .archive-page {
        width: 100%;
        min-height: 100%;
        padding: 30px;
        color: #171b24;
        background: #ffffff;
        font-family: Inter, Arial, sans-serif;
        box-sizing: border-box;
    }

    .archive-page *,
    .archive-page *::before,
    .archive-page *::after {
        box-sizing: border-box;
    }

    .archive-page__table-wrap {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e2e6ec;
        border-radius: 12px;
        min-height: 800px;
    }

    .archive-page__table {
        width: 100%;
        min-width: 1100px;
        border-collapse: collapse;
    }

    .archive-page__table th {
        height: 49px;
        padding: 0 16px;
        color: #758097;
        background: #f8f9fb;
        border-bottom: 1px solid #e2e6ec;
        font-size: 11px;
        font-weight: 600;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .archive-page__footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 18px;
        color: #7a8497;
        font-size: 12px;
    }

    .archive-page__pagination {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .page-btn {
        min-width: 35px;
        height: 35px;
        padding: 0 10px;
        color: #536073;
        background: #ffffff;
        border: 1px solid #e0e4ea;
        border-radius: 8px;
        cursor: pointer;
        font-size: 20px;
        padding-top: 5px;
        color: #ffffff;
        background: #1267f4;
    }

    .deactive {
        background-color: #f5f5f5;
        color: #c4c4c4;
    }

    @media (max-width: 800px) {
        .archive-page {
            padding: 20px 14px;
        }

        .archive-page__footer {
            align-items: flex-start;
            flex-direction: column;
            gap: 14px;
        }
    }
</style>