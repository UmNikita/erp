import { Client } from "./client";
import { Responsible } from "./kanban";

export interface Lead {
    id: number;
    name: string;
    date: string;
    client: string;
    manager: string;
    moneyAmount: number;
}

export interface LeadRequest {
    name: string;
    budget: number;
    product?: string;
    source?: string;
    next_action?: string;
    comment?: string;
    stage_id?: number;
    client_id?: number;
    client?: ClientLeadRequest | {};
    responsible_id?: number;
}

export interface ClientLeadRequest {
    name: string;
    phone?: string;
    email?: string;
}

export interface LeadResponse {
  id: number;
  name: string;
  budget: number;
  product: string;
  source: string;
  next_action: string;
  dateStart: string;
  date_next_action: string;
  comment: string;
  status: string;
  stage_id: number;
  responsible?: Responsible;
  client?: Client;
}