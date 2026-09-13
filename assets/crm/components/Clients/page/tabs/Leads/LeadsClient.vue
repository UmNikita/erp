<template>
    <section>
        <TableWrapper @set-page="setPage" @to="routeTableUrl" >
            <LeadTableHeader v-if="client" :name="client?.name" @btn-click="openModal(MODALS.CREATE_LEAD)" />
            <LeadTable :leads="leads" />
        </TableWrapper>
    </section>
    <LeadModal v-if="activeModal === MODALS.CREATE_LEAD" @close="closeModal"
    />
</template>

<script setup lang="ts">
    import TableWrapper from '../../../../paginationTable/TableWrapper.vue';
    import LeadModal from '../../../modals/LeadModal.vue';
    import { MODALS, useModal } from '../../../../../composables/useModal.ts';
    import { useCurrentClient } from '../../../../../composables/Clients/useCurrentClient.ts';
    import { useRouter } from 'vue-router';
    import LeadTable from './LeadTable.vue';
    import { clientLeadsUrl } from '../../../../../routes/client.ts';
    import LeadTableHeader from './LeadTableHeader.vue';

    const {activeModal, closeModal, openModal} = useModal();

    const {leads, client, leadsAllCount, loadLeads } = useCurrentClient();

    const router = useRouter();

    function routeTableUrl(page: number = 1) {
        router.push(clientLeadsUrl(page));
    }

    async function setPage(newPage: number, setStates: (allCount: number) => void) {
        await loadLeads(newPage);
        setStates(leadsAllCount.value);
    }
</script>