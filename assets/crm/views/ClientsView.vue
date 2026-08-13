<template>
    <div v-if="loading"></div>
    <div v-else>
        <div v-if="error"><Error /></div>
        <section class="shell" v-else>
            <ClientHeader />
            <ClientTable @delete="deleteClient" :clients="clients" />
            <ClientFooter :page="page" :range="range" :is-start="isStart()" 
            :is-end="isEnd()" :count="total" :client-count="allCount" />
        </section>
    </div>
    
    <ClientModal 
        v-if="activeModal === MODALS.CREATE_CLIENT" :error="generalError" :errors="errors"
        @close="closeModalClient" @submit="acceptAddClient"
    />
    
</template>

<script setup lang="ts">
    import { computed, ref, watch } from 'vue';
    import ClientFooter from '../components/Clients/table/ClientFooter.vue';
    import ClientHeader from '../components/Clients/table/ClientHeader.vue';
    import ClientTable from '../components/Clients/table/ClientTable.vue';
    import { Client, ClientRequest } from '../types/client.ts';
    import { getAllClients, deleteClient as deleteClientApi } from '../api/client.ts';
    import Error from '../components/Error.vue';
    import { useRoute } from 'vue-router';
    import { usePagination } from '../composables/usePagination.ts';
    import ClientModal from '../components/Clients/modals/ClientModal.vue';
    import { MODALS, useModal } from '../composables/useModal.ts';
    import { useClientForm } from '../composables/Clients/useClientForm.ts';

    const {activeModal, generalError, closeModal} = useModal();
    const errors = ref<Record<string, string>>({});
    const {createClient} = useClientForm();

    async function acceptAddClient(data: ClientRequest) {
        let client = await createClient(data, errors, generalError);
        if(client == null)
            return;
        clients.value.push(client);
        closeModal();
    }

    function closeModalClient() {
        errors.value = {};
        closeModal();
    }

    const LIMIT = 10;

    const clients = ref<Client[]>([]);
    const loading = ref(true);
    const error = ref(false);

    const total = ref(1);

    const route = useRoute();
    const page = computed(() => Number(route.query.page ?? 1));

    const {allCount, isStart, isEnd, range, setStates} = usePagination(page);

    async function setClients(page: number) {
        try{
            const clientsResponse = await getAllClients(LIMIT, page);
            clients.value = clientsResponse.clients;
            total.value = clientsResponse.pagination.count;
            if(page > 1)
                total.value += page * LIMIT;

            setStates(clientsResponse.pagination.allCount);
        }
        catch {
            error.value = true;
        }
    }

    async function deleteClient(client: Client) {
        if(!confirm("Вы действительно хотите удалить?"))
            return;
        try {
            await deleteClientApi(client);
        } catch (err) {
            alert("Возникла ошибка");
            return;
        }
        clients.value = clients.value.filter(value => value.id !== client.id);
    }

    watch(
        page,
        async (newPage) => {
            loading.value = true;
            await setClients(newPage);
            loading.value = false;
        },
        { immediate: true }
    );
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