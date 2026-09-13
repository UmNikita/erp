import { Pipeline, PipelineDetail } from "../types/pipeline";
import { Stage } from "../types/stage";

export function pipelineToDetail(pipeline: Pipeline, stages: Stage[] = []): PipelineDetail {
    return {
        id: pipeline.id,
        name: pipeline.name,
        stages: stages
    }
}

export function pipelineDetailToPipeline(pipeline: PipelineDetail): Pipeline {
    return {
        id: pipeline.id,
        name: pipeline.name
    }
}