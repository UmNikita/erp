import { StageRequest, StageResponse } from '../types/stage';
import api from './axios'

export async function createStage(stage: StageRequest): Promise<StageResponse>
{
    const response = await api.post('/stage', {
        name: stage.name,
        color: stage.color,
        pipeline_id: stage.pipeline_id
    });
    return response.data;
}

export async function renameStage(name: string, id: number): Promise<StageResponse>
{
    const response = await api.patch('/stage/'+id, {
        name: name
    });
    return response.data;
}

export async function deleteStage(id: number): Promise<StageResponse>
{
    const response = await api.delete('/stage/'+id);
    return response.data;
}

export async function moveStage(stageId: number, position: number): Promise<any>
{
    const response = await api.post('/stage/'+stageId+'/position', { position: position });
    return response.data;
}