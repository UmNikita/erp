import { defineStore } from 'pinia';
import { ref } from 'vue';
import { Pipeline, PipelineDetail } from '../types/pipeline';
import { getPipelines, getPipelinesDetail } from '../api/pipeline';
import { createPipeline as createPipelineApi, deletePipeline as deletePipelineApi, updatePipeline as updatePipelineApi } from "../api/pipeline";
import { pipelineToDetail } from '../mappers/pipelineMapper';

export const usePipelineStore = defineStore('pipeline', () => {

    const pipelines = ref<Pipeline[]>([]);
    const pipelinesDetail = ref<PipelineDetail[]>([]);
    const loading = ref(false);
    const error = ref(false);

    async function loadPipelines() {
        if (loading.value || pipelines.value.length > 0)
            return;

        loading.value = true;

        try {
            pipelines.value = await getPipelines();
            pipelinesDetail.value = await getPipelinesDetail();
        }
        catch (e) {
            error.value = true;
            
        } 
        finally {
            loading.value = false
        }
    }

    async function createPipeline(name: string): Promise<void> {
        const pipeline = await createPipelineApi(name);
        pipelines.value.push(pipeline);
        pipelinesDetail.value.push(pipelineToDetail(pipeline));
    }

    async function updatePipeline(newPipeline: Pipeline | PipelineDetail, pipelineId: number): Promise<void> {
        await updatePipelineApi(newPipeline);
        const pipeline = pipelines.value.find(record => record.id === pipelineId);
        const pipelineDetail = pipelinesDetail.value.find(record => record.id === pipelineId);
        if(pipeline != null && pipelineDetail != null) {
            pipeline.name = newPipeline.name;
            pipelineDetail.name = newPipeline.name;
        }
    }

    async function deletePipeline(pipeline: Pipeline | PipelineDetail): Promise<void> {
        await deletePipelineApi(pipeline);
        pipelines.value = pipelines.value.filter(value => value.id !== pipeline.id);
        pipelinesDetail.value = pipelinesDetail.value.filter(value => value.id !== pipeline.id);
    }

    async function deleteArrayPipelines(arr: Pipeline[] | PipelineDetail[]): Promise<void> {
        await Promise.all(
            arr.map(element => deletePipelineApi(element))
        );

        const ids = new Set(arr.map(element => element.id));

        pipelines.value = pipelines.value.filter(
            pipeline => !ids.has(pipeline.id)
        );

        pipelinesDetail.value = pipelinesDetail.value.filter(
            pipeline => !ids.has(pipeline.id)
        );
    }

    return {
        pipelines,
        pipelinesDetail,
        loading,
        error,
        loadPipelines,
        createPipeline,
        updatePipeline,
        deletePipeline,
        deleteArrayPipelines
    }
})