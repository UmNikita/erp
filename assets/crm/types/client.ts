import { Contact } from "./contact";

export interface Client {
    id: number;
    name: string;
    inn: string;
    field_of_activity: string;
    website: string;
    phone: string;
    email: string;
    city: string;
    channel: string;
    date_create: string;
    leads_count?: number;
    leads_amount?: number;
}

export interface ClientErrors {
    name: string | null;
    inn: string | null;
    field_of_activity: string | null;
    website: string | null;
    phone: string | null;
    email: string | null;
    city: string | null;
    channel: string | null;
}

export interface KPErrors {
    manager_name: string | null;
    manager_phone: string | null;
    contact_id: string | null;
    contact_email: string | null;
}

export interface ClientRequest {
    name: string;
    inn?: string;
    field_of_activity?: string;
    website?: string;
    phone?: string;
    email?: string;
    city?: string;
    channel?: string;
}


export interface GetClientsResponse {
    clients: Client[];
    pagination: Pagination;
}

export interface ClientCreateRequest {
    name: string;
    phone: string;
    email: string;
}

export interface Pagination {
    allCount: number;
    count: number;
    limit: number;
    page: number;
}

export interface ClientDetail {
    id: number;
    name: string;
    inn: string;
    field_of_activity: string;
    website: string;
    phone: string;
    email: string;
    city: string;
    channel: string;
    date_create: string;
    ltv: number;
    average_cheque: number;
    count_leads: number;
    amount_sum_leads: number;
    contacts: Contact[];
}

export interface ClientHistory {
    message: string;
    managerName: string;
    date: string;
}

export interface KpRequest {
    manager_name: string;
    manager_phone: string;
    contact_id?: number;
    contact_email?: string;
}

export interface EmailHistory {
    title: string;
    managerName: string;
    date: string;
    isSuccess: boolean;
}