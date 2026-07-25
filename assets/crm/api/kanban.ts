import { Kanban } from '../types/kanban';
import api from './axios'

export async function getKanban(pipelineId: number): Promise<Kanban>
{
    const response = await api.get('/kanban/' + pipelineId);
    return response.data;
}