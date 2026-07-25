import type { Lead } from './lead';

export interface Stage {
    id: number;
    name: string;
    color: string;
    leadCount: number;
    moneyAmount: number;
    leads: Lead[];
}