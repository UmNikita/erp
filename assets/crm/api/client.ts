import { Client } from '../types/client';
import api from './axios'

export async function searchClients(search: string): Promise<Client[]>
{
    const response = await api.get('/clients', {params: {search}});
    return response.data.clients;
}