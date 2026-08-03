import { Kanban } from "../types/kanban";
import { Lead, LeadResponse } from "../types/lead";
import { formatDate, formatResponseDate } from "../utils/fields";

export function leadResponseToUi(data: LeadResponse): Lead {
    return {
        id: data.id,
        name: data.name,
        date: formatResponseDate(data.dateStart),
        client: data.client ? data.client.name : '',
        manager: data.responsible ? data.responsible.name : '',
        moneyAmount: data.budget,
    }
}

export function mapKanban(response: Kanban): Kanban {
    return {
        leadsCount: response.leadsCount,
        moneyAmount: response.moneyAmount,

        stages: response.stages.map(stage => ({
            id: stage.id,
            name: stage.name,
            color: stage.color,
            leadCount: stage.leadCount,
            moneyAmount: stage.moneyAmount,

            leads: stage.leads.map(lead => ({
                id: lead.id,
                name: lead.name,
                date: formatDate(lead.date),
                client: lead.client,
                manager: lead.manager,
                moneyAmount: lead.moneyAmount
            }))
        }))
    };
}