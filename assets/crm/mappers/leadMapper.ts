import { Lead, LeadResponse } from "../types/lead";
import { formatResponseDate } from "../utils/fields";
export function leadResponseToUi(data: LeadResponse): Lead {
    return {
        id: data.id,
        name: data.name,
        date: formatResponseDate(data.dateStart),
        client: '',
        manager: '',
        moneyAmount: data.budget,
    }
}