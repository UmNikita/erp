export interface Contact {
    id: number;
    name: string;
    secondname: string;
    thirdname: string;
    position: string;
    phone: string;
    email: string;
    messenger: string;
    note: string;
    date_create: string;
}

export interface ContactRequest {
    name?: string;
    secondname?: string;
    thirdname?: string;
    position?: string;
    phone?: string;
    email?: string;
    messenger?: string;
    client_id?: number;
}

export interface ContactErrors {
    name: string | null;
    secondname: string | null;
    thirdname: string | null;
    position: string | null;
    phone: string | null;
    email: string | null;
    messenger: string | null;
    client_id: string | null;
}