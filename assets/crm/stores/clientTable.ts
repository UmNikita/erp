import { defineStore } from 'pinia';
import { ref } from 'vue';
import { Client, ClientRequest } from '../types/client';
import { getAllClients, createClient as createClientApi } from '../api/client';

export const useClientTableStore = defineStore('clientTable', () => {

    const clients = ref<Client[] | null>(null);
    const allCount = ref(0);
    const loading = ref(false);
    const error = ref(false);
    const oldPage = ref();
    const LIMIT = 10;

    async function loadClients(page: number) {
        if (loading.value || oldPage.value == page)
            return;

        loading.value = true;

        try {
            const clientsResponse = await getAllClients(LIMIT, page);
            clients.value = clientsResponse.clients;       
            allCount.value = clientsResponse.pagination.allCount;
            oldPage.value = page;
        }
        catch (e) {
            error.value = true;
            
        } 
        finally {
            loading.value = false
        }
    }

    async function refresh() {
        const clientsResponse = await getAllClients(LIMIT, oldPage.value);
        clients.value = clientsResponse.clients;       
        allCount.value = clientsResponse.pagination.allCount;
    }

    async function createClient(data: ClientRequest) {
        const client = await createClientApi(data);
        if(client != null)
            addClient(client);
    }

    function addClient(client?: Client) {
        if(!clients.value || !client)
            return;

        if(isLastPage())
            clients.value.push(client);
        
        allCount.value += 1;
    }

    function isLastPage(): boolean {
        if(allCount.value < LIMIT)
            return true;
        
        if(oldPage.value*LIMIT > allCount.value)
            return true;

        return false;
    }

    return {
        clients,
        allCount,
        loading,
        error,
        
        loadClients,
        refresh,
        createClient,
        addClient
    }
})