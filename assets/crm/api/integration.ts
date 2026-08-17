import { Token } from '../types/integration';
import api from './axios'

export async function getTokens(): Promise<Token[]>
{
    const response = await api.get('/integrations');
    return response.data.integrations;
}

export async function createToken(): Promise<Token>
{
    const response = await api.post('/integration');
    return response.data;
}

export async function reissue(id: number): Promise<Token>
{
    const response = await api.post(`/integration/${id}/reissue`);
    return response.data;
}

export async function revoke(id: number): Promise<Token>
{
    const response = await api.post(`/integration/${id}/revoke`);
    return response.data;
}

export async function active(id: number): Promise<Token>
{
    const response = await api.post(`/integration/${id}/active`);
    return response.data;
}