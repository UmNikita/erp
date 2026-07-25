import type { Stage } from './stage';

export interface Kanban {
    leadsCount: number;
    moneyAmount: number;
    stages: Stage[];
}