import { Client, ClientDetail } from "../types/client";
import { LeadDetail } from "../types/lead";

export function newClientToDetail(client: Client, lead: LeadDetail): ClientDetail {
    return {
        ...client,
        ltv: lead.budget,
        average_cheque: lead.budget,
        count_leads: 1,
        amount_sum_leads: lead.budget,
        contacts: []
    }
}
