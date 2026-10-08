import { defineStore } from 'pinia';
import api from './api';
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
        canAdmin: false,
        users: [] as Person[],
        tasks: [] as TaskItem[],
        shifts: [] as DutyShift[],
        schedules: [] as DutySchedule[],
        messages: [] as TaskMessage[],
        overdueReports: [] as TaskOverdueReport[],
        dashboard: null as TaskDashboard | null,
        feedback: [] as TaskFeedback[],
        audit: [] as TaskAudit[],
        auditTotal: 0,
        auditLoaded: false,
        auditLoading: false,
        loading: false,
        saving: false,
        error: '',
    }),
    actions: {
        async load() {
            this.loading = true;
            try {
                const { data } = await api.get<Bootstrap>('/bootstrap');
                this.currentUser = data.currentUser;
                this.canAdmin = data.canAdmin;
                this.users = data.users;
                this.tasks = data.tasks;
                this.shifts = data.shifts;
                this.schedules = data.schedules ?? [];
                this.messages = data.messages;
                this.overdueReports = data.overdueReports ?? [];
                this.dashboard = data.dashboard ?? null;
                this.feedback = data.feedback ?? [];
                this.error = '';
            } catch (error) { this.error = errorText(error); }
            finally { this.loading = false; }
        },
        async mutate(callback: () => Promise<unknown>): Promise<boolean> {
            this.saving = true;
            try { await callback(); await this.load(); if (this.auditLoaded) await this.loadAudit(true); return true; }
            catch (error) { this.error = errorText(error); return false; }
            finally { this.saving = false; }
        },
        createTask(payload: Record<string, unknown>) { return this.mutate(() => api.post('', payload)); },
        updateTask(id: number, payload: Record<string, unknown>) { return this.mutate(() => api.patch(`/${id}`, payload)); },
        createShift(payload: Record<string, unknown>) { return this.mutate(() => api.post('/shifts', payload)); },
        updateShift(id: number, payload: Record<string, unknown>) { return this.mutate(() => api.patch(`/shifts/${id}`, payload)); },
        createSchedule(payload: Record<string, unknown>) { return this.mutate(() => api.post('/schedules', payload)); },
        updateSchedule(id: number, payload: Record<string, unknown>) { return this.mutate(() => api.patch(`/schedules/${id}`, payload)); },
        addMessage(id: number, body: string) { return this.mutate(() => api.post(`/${id}/messages`, { body })); },
        reportOverdue(id: number, reason: string, newDueAt: string) { return this.mutate(() => api.post(`/${id}/report-overdue`, { reason, newDueAt })); },
        addFeedback(recipientId: number, body: string) { return this.mutate(() => api.post('/feedback', { recipientId, body })); },
        deleteFeedback(id: number) { return this.mutate(() => api.delete(`/feedback/${id}`)); },
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
