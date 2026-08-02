import { Pipeline, PipelineDetail, PipelineRequest } from "../types/pipeline";
import { Stage } from "../types/stage";

export function pipelineToDetail(pipeline: Pipeline, stages: Stage[] = []): PipelineDetail {
    return {
        id: pipeline.id,
        name: pipeline.name,
        stages: stages
    }
}

export function pipelineToRequest(pipeline: Pipeline | PipelineDetail): PipelineRequest {
    return {
        name: pipeline.name
    }
}

export function pipelineDetailToPipelineList(pipelines: PipelineDetail[]): Pipeline[] {
    let arr: Pipeline[] = []
    pipelines.forEach(element => {
        arr.push(pipelineDetailToPipeline(element));
    });
    return arr;
}

export function pipelineDetailToPipeline(pipeline: PipelineDetail): Pipeline {
    return {
        id: pipeline.id,
        name: pipeline.name
    }
}