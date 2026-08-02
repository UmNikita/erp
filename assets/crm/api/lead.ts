import { AxiosResponse } from 'axios';
import api from './axios'
import { LeadRequest, LeadResponse } from '../types/lead';
import { Kanban } from '../types/kanban';
import { formatDate } from '../utils/fields';

export async function updateStageLead(leadId: number, stageId: number): Promise<AxiosResponse>
{
    const response = await api.patch('/lead/' + leadId, {
        stage_id: stageId
    });
    return response;
}

export async function createStageLead(lead: LeadRequest): Promise<LeadResponse>
{
    const response = await api.post('/lead', lead);
    return response.data;
}

export function mapKanban(response: Kanban): Kanban {
    return {
        leadsCount: response.leadsCount,
        moneyAmount: response.moneyAmount,

        stages: response.stages.map(stage => ({
            id: stage.id,
            name: stage.name,
            color: stage.color,
            leadCount: stage.leadCount,
            moneyAmount: stage.moneyAmount,

            leads: stage.leads.map(lead => ({
                id: lead.id,
                name: lead.name,
                date: formatDate(lead.date),
                client: lead.client,
                manager: lead.manager,
                moneyAmount: lead.moneyAmount
            }))
        }))
    };
}