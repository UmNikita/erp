import { Client, ClientDetail, Pagination } from "./client";
import { Responsible } from "./kanban";
import { LeadStage } from "./stage";

export interface Lead {
    id: number;
    name: string;
    date: string;
    client: string;
    manager: string;
    moneyAmount: number;
}

export interface LeadErrors {
    name: string | null;
    budget: string | null;
    product: string | null;
    source: string | null;
    next_action: string | null;
    comment: string | null;
    stage_id: string | null;
    client_id: string | null;
    new_client: string | null;
    responsible_id: string | null;
}

export enum Status {
    won = 'won',
    lost = 'lost',
    active = 'active'
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
  stage: LeadStage;
  responsible?: Responsible;
  client?: Client;
}

export interface LeadDetail {
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
  stage: LeadStage;
  responsible?: Responsible;
  client?: ClientDetail;
}

export interface GetLeadsResponse {
    leads: LeadResponse[];
    pagination: Pagination;
}

export interface Message {
    id: number;
    responsible: Responsible;
    userId: number;
    date: string;
    message: string;
}