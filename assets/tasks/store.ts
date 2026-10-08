import { defineStore } from 'pinia';
import api from './api';
import { DEFAULT_BOARD_COLUMNS, type BoardColumn, type TaskStatus } from './types';
import type { Bootstrap, DutySchedule, DutyShift, Person, TaskItem, TaskMessage, TaskOverdueReport, TaskDashboard, TaskFeedback, TaskAudit, TaskAuditPage } from './types';

function errorText(error: unknown): string {
    if (typeof error === 'object' && error && 'response' in error) {
        const response = (error as { response?: { data?: { detail?: string; error?: string }; status?: number } }).response;
        return response?.data?.detail ?? response?.data?.error ?? `Не удалось сохранить (${response?.status ?? 'сеть'})`;
    }
    return 'Не удалось выполнить действие';
}

export const useTaskStore = defineStore('tasks', {
    state: () => ({
        currentUser: null as Person | null,
        boardColumns: DEFAULT_BOARD_COLUMNS.map((column) => ({ ...column })),
        canAdmin: false,
        users: [] as Person[],
        tasks: [] as TaskItem[],
        shifts: [] as DutyShift[],
        schedules: [] as DutySchedule[],
        messages: [] as TaskMessage[],
        overdueReports: [] as TaskOverdueReport[],
        dashboard: null as TaskDashboard | null,
        feedback: [] as TaskFeedback[],
        feedbackLoading: false,
        audit: [] as TaskAudit[],
        auditTotal: 0,
        auditLoaded: false,
        auditLoading: false,
        loading: false,
        saving: false,
        error: '',
    }),
    getters: {
        statusLabels: (state): Record<TaskStatus, string> => Object.fromEntries(state.boardColumns.map(column => [column.status, column.name])) as Record<TaskStatus, string>,
    },
    actions: {
        async load() {
            this.loading = true;
            try {
                const { data } = await api.get<Bootstrap>('/bootstrap');
                this.currentUser = data.currentUser;
                this.boardColumns = data.boardColumns ?? DEFAULT_BOARD_COLUMNS.map((column) => ({ ...column }));
                this.canAdmin = data.canAdmin;
                this.users = data.users;
                this.tasks = data.tasks;
                this.shifts = data.shifts;
                this.schedules = data.schedules ?? [];
                this.messages = data.messages;
                this.overdueReports = data.overdueReports ?? [];
                this.dashboard = data.dashboard ?? null;
                const readStates = new Map(this.feedback.map(item => [item.id, item.readAt]));
                this.feedback = (data.feedback ?? []).map(item => ({ ...item, readAt: item.readAt ?? readStates.get(item.id) ?? null }));
                this.error = '';
                return true;
            } catch (error) { this.error = errorText(error); return false; }
            finally { this.loading = false; }
        },
        async mutate(callback: () => Promise<unknown>): Promise<boolean> {
            this.saving = true;
            try { await callback(); const refreshed = await this.load(); if (this.auditLoaded) await this.loadAudit(true); return refreshed; }
            catch (error) { this.error = errorText(error); return false; }
            finally { this.saving = false; }
        },
        createTask(payload: Record<string, unknown>) { return this.mutate(() => api.post('', payload)); },
        saveBoardColumns(columns: BoardColumn[]) { return this.mutate(() => api.put('/board-columns', { columns })); },
        updateTask(id: number, payload: Record<string, unknown>) { return this.mutate(() => api.patch(`/${id}`, payload)); },
        createShift(payload: Record<string, unknown>) { return this.mutate(() => api.post('/shifts', payload)); },
        updateShift(id: number, payload: Record<string, unknown>) { return this.mutate(() => api.patch(`/shifts/${id}`, payload)); },
        createSchedule(payload: Record<string, unknown>) { return this.mutate(() => api.post('/schedules', payload)); },
        updateSchedule(id: number, payload: Record<string, unknown>) { return this.mutate(() => api.patch(`/schedules/${id}`, payload)); },
        addMessage(id: number, body: string) { return this.mutate(() => api.post(`/${id}/messages`, { body })); },
        reportOverdue(id: number, reason: string, newDueAt: string) { return this.mutate(() => api.post(`/${id}/report-overdue`, { reason, newDueAt })); },
        addFeedback(recipientId: number, body: string) { return this.mutate(() => api.post('/feedback', { recipientId, body })); },
        deleteFeedback(id: number) { return this.mutate(() => api.delete(`/feedback/${id}`)); },
        async refreshFeedback() {
            if (!this.currentUser || this.loading || this.feedbackLoading) return;
            this.feedbackLoading = true;
            try {
                const { data } = await api.get<TaskFeedback[]>('/feedback');
                const existing = new Map(this.feedback.map(item => [item.id, item]));
                const others = this.feedback.filter(item => item.recipient.id !== this.currentUser?.id);
                this.feedback = [...others, ...data.map(item => ({ ...item, readAt: item.readAt ?? existing.get(item.id)?.readAt ?? null }))];
            } catch { /* Retry silently on the next poll. */ }
            finally { this.feedbackLoading = false; }
        },
        async markFeedbackRead(id: number) {
            try {
                const { data } = await api.post<TaskFeedback>(`/feedback/${id}/read`);
                const item = this.feedback.find(item => item.id === id);
                if (item) item.readAt = data.readAt;
            } catch { /* Keep the unread indicator until the server confirms viewing. */ }
        },
        async loadAudit(reset = false) {
            if (!this.canAdmin || this.auditLoading) return;
            this.auditLoading = true;
            try {
                const offset = reset ? 0 : this.audit.length;
                const { data } = await api.get<TaskAuditPage>('/audit', { params: { offset } });
                this.audit = reset ? data.items : [...this.audit, ...data.items];
                this.auditTotal = data.total;
                this.auditLoaded = true;
            } catch (error) { this.error = errorText(error); }
            finally { this.auditLoading = false; }
        },
        clearError() { this.error = ''; },
    },
});
