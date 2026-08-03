import type { StageUI } from './stage';

export interface Kanban {
    leadsCount: number;
    moneyAmount: number;
    stages: StageUI[];
}

export interface Responsible {
    id: number;
    name: string;
    email: string;
}