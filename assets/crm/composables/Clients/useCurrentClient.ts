import { ref } from 'vue';
import { Client, ClientDetail, ClientHistory, EmailHistory } from '../../types/client';
import { LeadResponse } from '../../types/lead';
import { PipelineDetail } from '../../types/pipeline';
import { Responsible } from '../../types/kanban';
import { Contact } from '../../types/contact';
import { getDetailClients, getHistoryClient, getHistoryEmails } from '../../api/client';
import { getLeads } from '../../api/lead';
import {useClientTableStore} from '../../stores/clientTable';

const client = ref<ClientDetail | null>(null);
const leads = ref<LeadResponse[]>([]);
const leadsAllCount = ref(0);
const history = ref<ClientHistory[]>([]);
const historyEmails = ref<EmailHistory[]>([]);

const loadedReview = ref<boolean>(false);
const loadedEmails = ref<boolean>(false);
const loadedLeads = ref<boolean>(false);
const loadedHistory = ref<boolean>(false);

export function useCurrentClient() {

    const clientTableStore = useClientTableStore();

    async function load(clientId: number) {
        client.value = await getDetailClients(clientId);
        // const res = await getLeads(clientId);
        // leads.value = res.leads;
        // history.value = await getHistoryClient(clientId);
        // historyEmails.value = await getHistoryEmails(clientId);
    }

    async function loadReview(clientId: number) {
        if(loadedReview.value)
            return;
        if(!loadedLeads.value) {
            const res = await getLeads(clientId);
            leads.value = res.leads;
        }
        
        history.value = await getHistoryClient(clientId);
        loadedReview.value = true;
    }

    async function loadEmails(clientId: number) {
        if(loadedEmails.value)
            return;
        historyEmails.value = await getHistoryEmails(clientId);
        loadedEmails.value = true;
    }

    async function loadLeads(clientId: number) {
        if(!client.value)
            return
        if(loadedLeads.value)
            return;
        if(!loadedReview.value) {
            const res = await getLeads(client.value?.id);
            leads.value = res.leads;
            leadsAllCount.value = res.pagination.allCount;
        }
        loadedLeads.value = true;
    }

    async function loadHistory() {
        if(!client.value)
            return
        if(loadedHistory.value || loadedReview.value)
            return;
        history.value = await getHistoryClient(client.value?.id);
        loadedHistory.value = true;
    }

    function changeClientTable(clientData: Client) {
        const client = clientTableStore.clients?.find(client => client.id === clientData.id);
        if(client) {
            client.name = clientData.name;
            client.channel = clientData.channel;
            client.phone = clientData.phone;
            client.email = clientData.email;
        }
        
    }

    function newLead(lead: LeadResponse) {
        if(client.value) {
            if(leads.value.length < 10)
                leads.value.push(lead);
            client.value.count_leads += 1;
            client.value.amount_sum_leads += lead.budget;
            client.value.ltv += lead.budget;
        }
        
    }

    return {
        load,
        loadReview,
        newLead,
        changeClientTable,
        loadEmails,
        loadLeads,
        loadHistory,
        client,
        leads,
        leadsAllCount,
        history,
        historyEmails
    };
}