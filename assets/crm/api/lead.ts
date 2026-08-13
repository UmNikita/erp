import { AxiosResponse } from 'axios';
import api from './axios'
import { GetLeadsResponse, LeadRequest, LeadResponse } from '../types/lead';

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

export async function lostLead(id: number): Promise<AxiosResponse>
{
    const response = await api.patch('/lead/' + id, {
        status: 'lost'
    });
    return response;
}

export async function successLead(id: number): Promise<AxiosResponse>
{
    const response = await api.patch('/lead/' + id, {
        status: 'won'
    });
    return response;
}

export async function activeLead(id: number): Promise<AxiosResponse>
{
    const response = await api.patch('/lead/' + id, {
        status: 'active'
    });
    return response;
}

export async function deleteLead(id: number): Promise<AxiosResponse>
{
    const response = await api.delete('/lead/' + id);
    return response;
}

export async function getLeads(client_id?: number): Promise<GetLeadsResponse>
{
    const response = await api.get('/leads', {params: {client_id}});
    return response.data;
}

export async function getArchiveLeads(): Promise<GetLeadsResponse>
{
    const response = await api.get('/leads', {params: {archive: true}});
    return response.data;
}