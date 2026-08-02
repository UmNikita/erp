import { Pipeline, PipelineDetail, PipelineRequest } from '../types/pipeline';
import api from './axios'

export async function getPipelines(): Promise<Pipeline[]>
{
    const response = await api.get('/pipelines');
    return response.data.pipelines;
}

export async function getPipelinesDetail(): Promise<PipelineDetail[]>
{
    const response = await api.get('/pipelines-detail');
    return response.data.pipelines;
}

export async function createPipeline(pipeline: PipelineRequest): Promise<Pipeline>
{
    const response = await api.post('/pipeline', {
        name: pipeline.name
    });
    return response.data;
}

export async function deletePipeline(pipeline: Pipeline): Promise<void>
{
    const response = await api.delete('/pipeline/'+pipeline.id);
    return response.data;
}

export async function updatePipeline(pipeline: Pipeline): Promise<Pipeline>
{
    const response = await api.patch('/pipeline/'+pipeline.id, {
        name: pipeline.name
    });
    return response.data;
}