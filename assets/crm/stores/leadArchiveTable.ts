import { defineStore } from 'pinia';
import { ref } from 'vue';
import { LeadResponse } from '../types/lead';
import { getArchiveLeads } from '../api/lead';
import { deleteLead as deleteLeadApi } from '../api/lead';

export const useLeadTableStore = defineStore('leadArchiveTable', () => {

    const leads = ref<LeadResponse[] | null>(null);
    const allCount = ref(0);
    const loading = ref(false);
    const error = ref(false);
    const oldPage = ref();
    const LIMIT = 10;

    async function loadLeads(page: number) {
        if (loading.value || oldPage.value == page)
            return;

        loading.value = true;

        try {
            const leadsResponse = await getArchiveLeads(LIMIT, page);
            leads.value = leadsResponse.leads;       
            allCount.value = leadsResponse.pagination.allCount;
            oldPage.value = page;
        }
        catch (e) {
            error.value = true;
            
        } 
        finally {
            loading.value = false
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

    async function refresh() {
        const leadsResponse = await getArchiveLeads(LIMIT, oldPage.value);
        leads.value = leadsResponse.leads;       
        allCount.value = leadsResponse.pagination.allCount;
    }

    return {
        leads,
        allCount,
        loading,
        error,
        loadLeads,
        refresh
    }
})