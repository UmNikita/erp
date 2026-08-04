import { ref } from 'vue';
import type { Pipeline, PipelineDetail, PipelineBuffersDTO } from '../../../types/pipeline';
import { pipelineToDetail } from '../../../mappers/pipelineMapper';
import { Stage } from '../../../types/stage';

function updatePipelinesState(pipelines: Pipeline[], data: PipelineBuffersDTO) {
    return pipelines
    .filter(
        pipeline => !data.delete.some(deleted => deleted.id === pipeline.id)
    )
    .map(pipeline => {
        const updated = data.update.find(
            item => item.id === pipeline.id
        );

        return updated ?? pipeline;
    });
}

function updatePipelineDetailsState(pipelines: PipelineDetail[], data: PipelineBuffersDTO) {
    return pipelines
    .filter(
        pipeline => !data.delete.some(deleted => deleted.id === pipeline.id)
    )
    .map(pipeline => {
        const updated = data.update.find(
            item => item.id === pipeline.id
        );

        return updated
            ? {...pipeline, name: updated.name}
            : pipeline;
    });
}

const pipelines = ref<Pipeline[]>([]);
const pipelinesDetail = ref<PipelineDetail[]>([]);

export function usePipelines() {

    function addPipeline(pipeline: Pipeline) {
        pipelines.value.push(pipeline);
        pipelinesDetail.value.push(pipelineToDetail(pipeline));
    }

    function updatePipelines(data: PipelineBuffersDTO) {
        pipelines.value = updatePipelinesState(pipelines.value, data);
        pipelinesDetail.value = updatePipelineDetailsState(pipelinesDetail.value, data);
    }

    function addStageToDetailPipeline(pipelineId: number, stage: Stage) {
        const pipeline = pipelinesDetail.value.find(pipeline => pipeline.id === pipelineId);
        if(pipeline != null)
        {
            pipeline.stages.push(stage);
        }
    }

    function renameStageForDetailPipeline(pipelineId: number, stageId: number, newName: string) {
        const pipeline = pipelinesDetail.value.find(pipeline => pipeline.id === pipelineId);
        if(pipeline != null) {
            const stage = pipeline?.stages.find(stage => stage.id === stageId);
            if(stage != null)
            {
                stage.name = newName;
            }
        }
        
    }

    function deleteStageForDetailPipeline(pipelineId: number, stageId: number) {
        const pipeline = pipelinesDetail.value.find(pipeline => pipeline.id === pipelineId);
        if(pipeline != null)
            pipeline.stages = pipeline.stages.filter(stage => stage.id !== stageId);
    }

    return {
        pipelines,
        pipelinesDetail,
        addPipeline,
        updatePipelines,
        addStageToDetailPipeline,
        renameStageForDetailPipeline,
        deleteStageForDetailPipeline
    };
}