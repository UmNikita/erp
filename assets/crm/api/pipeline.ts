import { Pipeline } from '../types/pipeline';
import api from './axios'

export async function getPipelines(): Promise<Pipeline[]>
{
    const response = await api.get('/pipelines');
    return response.data.pipelines;
}