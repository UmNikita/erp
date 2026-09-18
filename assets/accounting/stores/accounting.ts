import { computed, ref } from 'vue';
import { defineStore } from 'pinia';
import api from '../api';
import type {
    AccountingOperation,
    AuditEntry,
    BootstrapPayload,
    Category,
    CategoryPayload,
    OperationPayload,
    UserSummary,
} from '../types';
import { monthRange } from '../utils/dateRange';

function initialPeriod(): { from: string; to: string } {
    return monthRange();
}

export const useAccountingStore = defineStore('accounting', () => {
    const categories = ref<Category[]>([]);
    const operations = ref<AccountingOperation[]>([]);
    const audit = ref<AuditEntry[]>([]);
    const users = ref<UserSummary[]>([]);
    const currentUser = ref<UserSummary | null>(null);
    const period = ref(initialPeriod());
    const loading = ref(false);
    const saving = ref(false);
    const error = ref('');

    const activeCategories = computed(() => categories.value.filter((item) => item.active));
    const archivedCategories = computed(() => categories.value.filter((item) => !item.active));
    const totalIncome = computed(() => operations.value.filter((item) => item.included && item.type === 'income').reduce((sum, item) => sum + item.amount, 0));
    const totalExpense = computed(() => operations.value.filter((item) => item.included && item.type === 'expense').reduce((sum, item) => sum + item.amount, 0));
    const balance = computed(() => totalIncome.value - totalExpense.value);

    function allocated(operation: AccountingOperation): number {
        return operation.allocations.reduce((sum, item) => sum + item.amount, 0);
    }

    function remaining(operation: AccountingOperation): number {
        return Math.max(0, operation.amount - allocated(operation));
    }

    async function load(from = period.value.from, to = period.value.to): Promise<void> {
        loading.value = true;
        error.value = '';
        period.value = { from, to };
        try {
            const { data } = await api.get<BootstrapPayload>('/bootstrap', { params: { from, to } });
            categories.value = data.categories;
            operations.value = data.transactions;
            audit.value = data.audit;
            users.value = data.users;
            currentUser.value = data.currentUser;
        } catch (reason: any) {
            error.value = reason.response?.data?.error || 'Не удалось загрузить данные бухгалтерии';
            throw reason;
        } finally {
            loading.value = false;
        }
    }

    async function mutate<T>(request: () => Promise<T>): Promise<T> {
        saving.value = true;
        error.value = '';
        try {
            const result = await request();
            await load();
            return result;
        } catch (reason: any) {
            error.value = reason.response?.data?.error || 'Не удалось сохранить изменения';
            throw reason;
        } finally {
            saving.value = false;
        }
    }

    async function createOperation(payload: OperationPayload): Promise<void> {
        await mutate(() => api.post('/transactions', payload));
    }

    async function updateOperation(id: number, payload: Partial<OperationPayload>): Promise<void> {
        await mutate(() => api.patch(`/transactions/${id}`, payload));
    }

    async function deleteOperation(id: number): Promise<void> {
        await mutate(() => api.delete(`/transactions/${id}`));
    }

    async function allocateOperation(operation: AccountingOperation, categoryId: number): Promise<void> {
        const amount = remaining(operation);
        if (amount <= 0) return;
        await mutate(() => api.post(`/transactions/${operation.id}/allocate`, { categoryId, amount }));
    }

    async function createCategory(payload: CategoryPayload): Promise<void> {
        await mutate(() => api.post('/categories', payload));
    }

    async function updateCategory(id: number, payload: Partial<CategoryPayload>): Promise<void> {
        await mutate(() => api.patch(`/categories/${id}`, payload));
    }

    async function deleteCategory(id: number): Promise<void> {
        await mutate(() => api.delete(`/categories/${id}`));
    }

    async function reloadAudit(): Promise<void> {
        const { data } = await api.get<AuditEntry[]>('/audit');
        audit.value = data;
    }

    async function restoreAuditEntry(id: number): Promise<void> {
        await mutate(() => api.post(`/audit/${id}/restore`));
    }

    return {
        categories, operations, audit, users, currentUser, period, loading, saving, error,
        activeCategories, archivedCategories, totalIncome, totalExpense, balance,
        load, allocated, remaining, createOperation, updateOperation, deleteOperation, allocateOperation,
        createCategory, updateCategory, deleteCategory, reloadAudit, restoreAuditEntry,
    };
});
