import { onMounted, ref } from 'vue';
import type { Pipeline, PipelineDetail, PipelineModalUpdateResponeDTO } from '../../types/pipeline';
import { pipelineToDetail } from '../../mappers/pipelineMapper';
import { deletePipeline, getPipelines, getPipelinesDetail, updatePipeline } from '../../api/pipeline';
import { Stage } from '../../types/stage';

function updatePipelinesState(pipelines: Pipeline[], data: PipelineModalUpdateResponeDTO) {
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

function updatePipelineDetailsState(pipelines: PipelineDetail[], data: PipelineModalUpdateResponeDTO) {
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


export function usePipelines() {

    const pipelines = ref<Pipeline[]>([]);
    const pipelinesDetail = ref<PipelineDetail[]>([]);
    const loading = ref(true);
    const error = ref(false);

    onMounted(async () => {
        try{
            pipelines.value = await getPipelines();
            pipelinesDetail.value = await getPipelinesDetail();
        }
        catch {
            error.value = true;
        }
        finally {
            loading.value = false;
        }
    })

    function updatePipelinesAfterCreate(pipeline: Pipeline) {
        pipelines.value.push(pipeline);
        pipelinesDetail.value.push(pipelineToDetail(pipeline));
    }

    function updatePipelinesAfterUpdate(data: PipelineModalUpdateResponeDTO) {
        pipelines.value = updatePipelinesState(pipelines.value, data);
        pipelinesDetail.value = updatePipelineDetailsState(pipelinesDetail.value, data);
    }

    function updatePipelinesDetailAfterCreateStage(pipelineId: number, stage: Stage) {
        const pipeline = pipelinesDetail.value.find(pipeline => pipeline.id === pipelineId);
        if(pipeline != null)
        {
            pipeline.stages.push(stage);
        }
    }

    function renameStageForPipelinesDetail(pipelineId: number, stageId: number, newName: string) {
        const pipeline = pipelinesDetail.value.find(pipeline => pipeline.id === pipelineId);
        if(pipeline != null) {
            const stage = pipeline?.stages.find(stage => stage.id === stageId);
            if(stage != null)
            {
                stage.name = newName;
            }
        }
        
    }

    function deleteStageForPipelinesDetail(pipelineId: number, stageId: number) {
        const pipeline = pipelinesDetail.value.find(pipeline => pipeline.id === pipelineId);
        if(pipeline != null)
        {
            pipeline.stages = pipeline.stages.filter(stage => stage.id !== stageId);
        }
    }


    function getRequests(data: PipelineModalUpdateResponeDTO): Promise<unknown>[] {
        const requests: Promise<unknown>[] = [];

        data.delete.forEach(pipeline => {
            requests.push(
                deletePipeline(pipeline)
            );
        });

        data.update.forEach(pipeline => {
            requests.push(
                updatePipeline(pipeline)
            );
        });
        
        return requests;
    }

    return {
        loading,
        pipelines,
        pipelinesDetail,
        updatePipelinesAfterCreate,
        updatePipelinesAfterUpdate,
        getRequests,
        updatePipelinesDetailAfterCreateStage,
        renameStageForPipelinesDetail,
        deleteStageForPipelinesDetail,
        error
    };
}