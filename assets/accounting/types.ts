export type OperationType = 'income' | 'expense';

export interface UserSummary {
    id: number;
    name: string;
    email: string;
}

export interface Category {
    id: number;
    name: string;
    type: OperationType;
    icon: string;
    color: string;
    active: boolean;
    createdAt: string;
    createdBy: UserSummary | null;
}

export interface Allocation {
    id: number;
    categoryId: number;
    amount: number;
}

export interface AccountingOperation {
    id: number;
    type: OperationType;
    amount: number;
    date: string;
    counterparty: string;
    comment: string | null;
    included: boolean;
    createdBy: UserSummary | null;
    createdAt: string;
    updatedAt: string;
    allocations: Allocation[];
}

export interface AuditEntry {
    id: number;
    user: UserSummary | null;
    action: string;
    entityType: string;
    entityId: number | null;
    payload: Record<string, unknown>;
    createdAt: string;
    restoredAt: string | null;
}

export interface BootstrapPayload {
    categories: Category[];
    transactions: AccountingOperation[];
    audit: AuditEntry[];
    users: UserSummary[];
    currentUser: UserSummary;
}

export interface OperationPayload {
    type: OperationType;
    amount: number;
    date: string;
    counterparty: string;
    comment: string | null;
    included: boolean;
    createdById?: number | null;
    categoryId?: number | null;
}

export interface CategoryPayload {
    name: string;
    type: OperationType;
    icon: string;
    color: string;
    active?: boolean;
}
