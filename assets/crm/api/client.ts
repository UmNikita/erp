import { Axios } from 'axios';
import { Client, ClientDetail, ClientHistory, ClientRequest, EmailHistory, GetClientsResponse } from '../types/client';
import { KpRequest } from '../types/client';
import api from './axios'

export async function searchClients(search: string): Promise<Client[]>
{
    const response = await api.get('/clients', {params: {search}});
    return response.data.clients;
}

export async function getAllClients(limit: number = 10, page: number = 1): Promise<GetClientsResponse>
{
    const response = await api.get('/clients', {params: {limit, page}});
    return response.data;
}

export async function getDetailClients(id: Number): Promise<ClientDetail>
{
    const response = await api.get('/client/'+id);
    return response.data;
}

export async function createClient(data: ClientRequest): Promise<Client>
{
    const response = await api.post('/client', data);
    return response.data;
}

export async function updateClient(id: number, data: ClientRequest): Promise<Client>
{
    const response = await api.patch('/client/'+id, data);
    return response.data;
}

export async function deleteClient(client: Client): Promise<void>
{
    const response = await api.delete('/client/'+client.id);
    return response.data;
}

export async function getHistoryClient(clientID: number): Promise<ClientHistory[]>
{
    const response = await api.get(`/client/${clientID}/history`);
    return response.data.history;
}

export async function sendKP(clientID: number, data: KpRequest): Promise<Axios>
{
    const response = await api.post(`/client/${clientID}/email/kp`, data);
    return response.data;
}

export async function getHistoryEmails(clientID: number): Promise<EmailHistory[]>
{
    const response = await api.get(`/client/${clientID}/email/history`);
    return response.data.emails;
}