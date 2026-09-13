<template>
    <TableWrapper @set-page="setPage" @to="routeTableUrl" v-if="leads" >
        <Header />
        <div class="archive-page__table-wrap">
            <table class="archive-page__table">
                <thead>
                    <tr>
                        <th>Сделка</th>
                        <th>Клиент</th>
                        <th>Воронка</th>
                        <th>Сумма</th>
                        <th>Ответственный</th>
                        <th>Результат</th>
                        <th>Дата архивации</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    <Row v-for="lead in leadTableStore.leads" :lead="lead" />
                </tbody>
            </table>
        </div>
    </TableWrapper>
</template>

<script setup lang="ts">
    import { ref } from 'vue';
    import Header from './Header.vue';
    import Row from './Row.vue';
    import { useRouter } from 'vue-router';
    import { archiveTableUrl } from '../../routes/lead.ts';
    import { useLeadTableStore } from '../../stores/leadArchiveTable.ts';
    import TableWrapper from '../paginationTable/TableWrapper.vue';

    const leads = ref([]);
   
    const router = useRouter();
    
    const leadTableStore = useLeadTableStore();

    async function setPage(newPage: number, setStates: (allCount: number) => void) {
        await leadTableStore.loadLeads(newPage);
        setStates(leadTableStore.allCount);
    }

    function routeTableUrl(page: number = 1) {
        router.push(archiveTableUrl(page));
    }
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