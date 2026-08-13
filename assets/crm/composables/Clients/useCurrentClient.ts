import { ref } from 'vue';
import { ClientDetail, ClientHistory, EmailHistory } from '../../types/client';
import { LeadResponse } from '../../types/lead';
import { PipelineDetail } from '../../types/pipeline';
import { Responsible } from '../../types/kanban';
import { Contact } from '../../types/contact';
import { getDetailClients, getHistoryClient, getHistoryEmails } from '../../api/client';
import { getLeads } from '../../api/lead';
import { getPipelinesDetail } from '../../api/pipeline';
import { getResponsibles } from '../../api/kanban';

const client = ref<ClientDetail | null>(null);
const leads = ref<LeadResponse[]>([]);
const pipelines = ref<PipelineDetail[]>([]);
const responsibles = ref<Responsible[]>([]);
const history = ref<ClientHistory[]>([]);
const historyEmails = ref<EmailHistory[]>([]);

export function useCurrentClient() {

    async function load(clientId: number) {
        client.value = await getDetailClients(clientId);
        leads.value = await getLeads(clientId);
        pipelines.value = await getPipelinesDetail();
        responsibles.value = await getResponsibles()
        history.value = await getHistoryClient(clientId);
        historyEmails.value = await getHistoryEmails(clientId);
    }

    function newLead(lead: LeadResponse) {
        if(client.value) {
            leads.value.push(lead);
            client.value.count_leads += 1;
            client.value.amount_sum_leads += lead.budget;
            client.value.ltv += lead.budget;
        }
        
    }

    function newContact(contact: Contact) {
        client.value?.contacts.push(contact);
    }

    return {
        load,
        newLead,
        newContact,
        client,
        leads,
        pipelines,
        responsibles,
        history,
        historyEmails
    };
}