import { Stage } from "./stage";

export interface Pipeline {
    id: number;
    name: string;
}

export interface PipelineRequest {
    name: string;
}

export interface PipelineModalUpdateListElementDTO {
    id: number;
    name: string;
    haveStages: boolean;
}

export interface PipelineBuffersDTO {
    update: Pipeline[];
    delete: Pipeline[];
}

export interface PipelineDetail {
    id: number;
    name: string;
    stages: Stage[];
}