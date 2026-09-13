<template>
    <div v-if="clientTableStore.loading"></div>
    <div v-else>
        <div v-if="clientTableStore.error"><Error /></div>
        <TableWrapper @set-page="setPage" @to="routeTableUrl">
            <ClientTableHeader @btn-click="openModal(MODALS.CREATE_CLIENT)" />
            <ClientTable />
        </TableWrapper>
    </div>
    <ClientModal :is-submit="false" v-if="activeModal === MODALS.CREATE_CLIENT" @close="closeModal" />
</template>

<script setup lang="ts">
    import ClientTable from '../components/Clients/table/ClientTable.vue';
    import ClientTableHeader from '../components/Clients/table/ClientTableHeader.vue';
    import Error from '../components/Error.vue';
    import TableWrapper from '../components/paginationTable/TableWrapper.vue';
    import { MODALS, useModal } from '../composables/useModal.ts';
    import { useClientTableStore } from '../stores/clientTable.ts';
    import ClientModal from '../components/Clients/modals/ClientModal.vue';
    import { clientTableUrl } from '../routes/client.ts';
    import { useRouter } from 'vue-router';

    const clientTableStore = useClientTableStore();
    const router = useRouter();

    const { closeModal, activeModal, openModal } = useModal();

    function routeTableUrl(page: number = 1) {
        router.push(clientTableUrl(page));
    }

    async function setPage(newPage: number, setStates: (allCount: number) => void) {
        await clientTableStore.loadClients(newPage);
        setStates(clientTableStore.allCount);
    }
</script>

<style scoped>

.shell {
    min-height: calc(100vh - 44px);
    padding: 28px;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 16px;
    box-shadow: 0 5px 18px rgba(27, 39, 60, .06);
}

    @media(max-width:850px){
        .shell{
            min-height:100vh;
            padding:20px 14px;
            border-radius:0
        }
    }
</style>