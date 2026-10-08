import { taskAssignees, taskCompletedFor, type Person, type TaskItem, type TaskOverdueReport } from '../types';

export interface EmployeeStats {
    person: Person;
    assigned: number;
    done: number;
    failed: number;
    dueDated: number;
    onTime: number;
    moved: number;
    reports: number;
    overdue: number;
    completionRate: number | null;
    onTimeRate: number | null;
    transferRate: number | null;
}

export interface AdminAnalytics {
    employees: EmployeeStats[];
    totals: Omit<EmployeeStats, 'person'>;
    failedTasks: TaskItem[];
    overdueTasks: TaskItem[];
    transferReports: TaskOverdueReport[];
}

const moscowParts = new Intl.DateTimeFormat('en-US', { timeZone: 'Europe/Moscow', year: 'numeric', month: '2-digit', day: '2-digit' });

export function moscowDay(value: string | null): string | null {
    if (!value) return null;
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return null;
    const parts = Object.fromEntries(moscowParts.formatToParts(date).map((part) => [part.type, part.value]));
    return `${parts.year}-${parts.month}-${parts.day}`;
}

export function inPeriod(value: string | null, from: string, to: string): boolean {
    const day = moscowDay(value);
    return !!day && day >= from && day <= to;
}

export function reportOwner(report: TaskOverdueReport, task?: TaskItem): Person | null {
    return report.assigneeAtReport ?? report.author ?? task?.assignee ?? null;
}

export function reportOwnerIds(report: TaskOverdueReport, task?: TaskItem): number[] {
    return report.assigneeAtReportIds?.length ? report.assigneeAtReportIds : (reportOwner(report, task)?.id ? [reportOwner(report, task)!.id] : []);
}

export function reportOwnerNames(report: TaskOverdueReport, people: Person[], task?: TaskItem): string {
    const names = reportOwnerIds(report, task).map((id) => people.find((person) => person.id === id)?.name ?? (reportOwner(report, task)?.id === id ? reportOwner(report, task)?.name : null)).filter(Boolean);
    return names.join(', ') || 'Неизвестен';
}

export function buildAdminAnalytics(tasks: TaskItem[], reports: TaskOverdueReport[], people: Person[], selectedIds: number[], from: string, to: string, now = Date.now()): AdminAnalytics {
    const selected = new Set(selectedIds);
    const tasksById = new Map(tasks.map((task) => [task.id, task]));
    const movedTaskIds = new Set(reports.map((report) => report.taskId));
    const isClosed = (task: TaskItem) => inPeriod(task.completedAt, from, to) && ['done', 'not_done'].includes(task.status);
    const isOverdue = (task: TaskItem) => !!task.dueAt && inPeriod(task.dueAt, from, to) && new Date(task.dueAt).getTime() < now && !['done', 'not_done'].includes(task.status);
    const onTimeTask = (task: TaskItem) => task.status === 'done' && !!task.dueAt && !!task.completedAt && new Date(task.completedAt).getTime() <= new Date(task.dueAt).getTime() && !movedTaskIds.has(task.id);
    const employees = people.filter((person) => selected.has(person.id)).map((person) => {
        const assigned = tasks.filter((task) => taskAssignees(task).some((owner) => owner.id === person.id) && inPeriod(task.createdAt, from, to)).length;
        const closed = tasks.filter((task) => taskCompletedFor(task, person.id) && isClosed(task));
        const done = closed.filter((task) => task.status === 'done').length;
        const failed = closed.filter((task) => task.status === 'not_done').length;
        const dueDated = closed.filter((task) => task.dueAt).length;
        const onTime = closed.filter(onTimeTask).length;
        const moved = closed.filter((task) => task.dueAt && movedTaskIds.has(task.id)).length;
        const reportCount = reports.filter((report) => reportOwnerIds(report, tasksById.get(report.taskId)).includes(person.id) && inPeriod(report.createdAt, from, to)).length;
        const overdue = tasks.filter((task) => taskAssignees(task).some((owner) => owner.id === person.id) && isOverdue(task)).length;
        return {
            person, assigned, done, failed, dueDated, onTime, moved, reports: reportCount, overdue,
            completionRate: done + failed ? Math.round(done * 100 / (done + failed)) : null,
            onTimeRate: dueDated ? Math.round(onTime * 100 / dueDated) : null,
            transferRate: dueDated ? Math.round(moved * 100 / dueDated) : null,
        };
    });
    const selectedAssignment = (task: TaskItem) => taskAssignees(task).some((person) => selected.has(person.id));
    const selectedCompletion = (task: TaskItem) => (task.completedForIds?.length ? task.completedForIds : (task.completedFor ? [task.completedFor.id] : [])).some((id) => selected.has(id));
    const selectedClosed = tasks.filter((task) => selectedCompletion(task) && isClosed(task));
    const done = selectedClosed.filter((task) => task.status === 'done').length;
    const failed = selectedClosed.filter((task) => task.status === 'not_done').length;
    const dueDated = selectedClosed.filter((task) => task.dueAt).length;
    const onTime = selectedClosed.filter(onTimeTask).length;
    const moved = selectedClosed.filter((task) => task.dueAt && movedTaskIds.has(task.id)).length;
    const transferReports = reports.filter((report) => reportOwnerIds(report, tasksById.get(report.taskId)).some((id) => selected.has(id)) && inPeriod(report.createdAt, from, to)).sort((a, b) => b.createdAt.localeCompare(a.createdAt));
    const overdueTasks = tasks.filter((task) => selectedAssignment(task) && isOverdue(task)).sort((a, b) => (a.dueAt ?? '').localeCompare(b.dueAt ?? ''));
    const totals = {
        assigned: tasks.filter((task) => selectedAssignment(task) && inPeriod(task.createdAt, from, to)).length,
        done, failed, dueDated, onTime, moved, reports: transferReports.length, overdue: overdueTasks.length,
        completionRate: done + failed ? Math.round(done * 100 / (done + failed)) : null,
        onTimeRate: dueDated ? Math.round(onTime * 100 / dueDated) : null,
        transferRate: dueDated ? Math.round(moved * 100 / dueDated) : null,
    };
    const failedTasks = selectedClosed.filter((task) => task.status === 'not_done').sort((a, b) => (b.completedAt ?? '').localeCompare(a.completedAt ?? ''));
    return { employees, totals, failedTasks, overdueTasks, transferReports };
}
