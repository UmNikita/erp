import { AxiosResponse } from 'axios';
import api from './axios'
import { LeadRequest, LeadResponse } from '../types/lead';

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