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
}

export interface ClientCreateRequest {
    name: string;
    phone: string;
    email: string;
}