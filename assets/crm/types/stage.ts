import type { Lead } from './lead';

export interface StageUI {
    id: number;
    name: string;
    color: string;
    sequence: number;
    leadCount: number;
    moneyAmount: number;
    leads: Lead[];
}

export interface StageResponse {
    id: number;
    name: string;
    color: string;
    sequence: number;
    pipeline_id: number;
}

export interface StageRequest {
    name: string;
    color: string;
    pipeline_id?: number;
}

export interface Stage {
    id: number;
    name: string;
    sequence: number;
}