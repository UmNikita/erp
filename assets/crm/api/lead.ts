import { AxiosResponse } from 'axios';
import api from './axios'
import { GetLeadsResponse, LeadDetail, LeadRequest, LeadResponse, Message } from '../types/lead';

export async function updateStageLead(leadId: number, stageId: number): Promise<AxiosResponse>
{
    const response = await api.patch('/lead/' + leadId, {
        stage_id: stageId
    });
    return response;
}

export async function getLeadMessages(leadId: number): Promise<Message[]>
{
    const response = await api.get(`/lead/${leadId}/messages`);
    return response.data.leadMessages;
}

export async function sendLeadMessage(message: string, leadId: number): Promise<Message>
{
    const response = await api.post('/lead/message', {message: message, lead_id: leadId});
    return response.data;
}

export async function updateResponsibleLead(leadId: number, responsibleId: number): Promise<AxiosResponse>
{
    const response = await api.patch('/lead/' + leadId, {
        responsible_id: responsibleId
    });
    return response;
}

export async function updateLead(leadId: number, data: LeadRequest): Promise<AxiosResponse>
{
    const response = await api.patch('/lead/' + leadId, data);
    return response;
}

export async function setClientLead(leadId: number, clientId: number): Promise<AxiosResponse>
{
    const response = await api.patch('/lead/' + leadId, {
        client_id: clientId
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

export async function getLeadDetail(id?: number): Promise<LeadDetail>
{
    const response = await api.get('/lead/' + id);
    return response.data;
}


export async function getArchiveLeads(): Promise<GetLeadsResponse>
{
    const response = await api.get('/leads', {params: {archive: true}});
    return response.data;
}