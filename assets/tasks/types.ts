export interface Person { id: number; name: string }
export type TaskStatus = 'open' | 'in_progress' | 'review' | 'done' | 'not_done';
export type TaskPriority = 'low' | 'normal' | 'high';
export interface BoardColumn { status: TaskStatus; name: string; color: string }
export const DEFAULT_BOARD_COLUMNS: BoardColumn[] = [
    { status: 'open', name: 'К работе', color: '#718096' },
    { status: 'in_progress', name: 'В работе', color: '#3d70f5' },
    { status: 'review', name: 'На проверке', color: '#ed9a32' },
    { status: 'done', name: 'Выполнено', color: '#1faf79' },
    { status: 'not_done', name: 'Не выполнено', color: '#8993a4' },
];
export interface TaskItem {
    id: number;
    title: string;
    description: string | null;
    result: string | null;
    status: TaskStatus;
    priority: TaskPriority;
    color: string;
    dueAt: string | null;
    assignee: Person | null;
    assignees: Person[];
    createdBy: Person | null;
    requestedBy: Person | null;
    dutyShiftId: number | null;
    createdAt: string;
    updatedAt: string;
    completedAt: string | null;
    completedFor: Person | null;
    completedForIds: number[];
}
export function taskAssignees(task: TaskItem): Person[] { return task.assignees?.length ? task.assignees : (task.assignee ? [task.assignee] : []); }
export function isTaskAssignee(task: TaskItem, personId: number | undefined): boolean { return !!personId && taskAssignees(task).some((person) => person.id === personId); }
export function taskCompletedFor(task: TaskItem, personId: number): boolean { return (task.completedForIds?.length ? task.completedForIds : (task.completedFor ? [task.completedFor.id] : [])).includes(personId); }
export interface TaskAuditChange { before: string | string[] | null; after: string | string[] | null }
export interface TaskAudit {
    id: number;
    taskId: number | null;
    taskTitle: string;
    actor: Person | null;
    action: 'created' | 'updated' | 'message_added' | 'deadline_moved';
    changes: Record<string, TaskAuditChange | string>;
    createdAt: string;
}
export interface TaskAuditPage { items: TaskAudit[]; total: number }
export interface DutyShift {
    id: number;
    title: string;
    assignee: Person;
    startsAt: string;
    endsAt: string;
    checklist: string[];
    scheduleId: number | null;
    color: string;
    comment: string | null;
}
export interface DutyDay { enabled: boolean; start: string; end: string; userIds: number[] }
export interface DutyTaskTemplate { id: string; title: string; description: string; priority: TaskPriority }
export interface DutySchedule {
    id: number;
    title: string;
    color: string;
    comment: string | null;
    active: boolean;
    sameTime: boolean;
    days: DutyDay[];
    taskTemplates: DutyTaskTemplate[];
    effectiveFrom: string;
}
export interface TaskMessage {
    id: number;
    taskId: number;
    author: Person | null;
    body: string;
    createdAt: string;
}
export interface TaskOverdueReport {
    id: number;
    taskId: number;
    taskTitle: string;
    author: Person | null;
    reason: string;
    assigneeAtReport: Person | null;
    assigneeAtReportIds: number[];
    previousDueAt: string;
    newDueAt: string;
    createdAt: string;
}
export interface DashboardDay { date: string; done: number; failed: number }
export interface DashboardPersonal {
    done: number; failed: number; onTime: number; dueDated: number; moved: number;
    onTimeRate: number | null; transferRate: number | null; completionRate: number | null;
}
export interface RankingRow {
    person: Person; rank: number; done: number; failed: number; onTime: number;
    dueDated: number; moved: number; completionRate: number | null; score: number;
}
export interface TaskDashboard { daily: DashboardDay[]; personal: DashboardPersonal; ranking: RankingRow[] }
export interface TaskFeedback { id: number; recipient: Person; author: Person | null; body: string; createdAt: string; readAt: string | null }
export interface Bootstrap {
    boardColumns: BoardColumn[];
    currentUser: Person;
    canAdmin: boolean;
    users: Person[];
    tasks: TaskItem[];
    shifts: DutyShift[];
    schedules: DutySchedule[];
    messages: TaskMessage[];
    overdueReports: TaskOverdueReport[];
    dashboard: TaskDashboard;
    feedback: TaskFeedback[];
}
export const STATUS_LABELS: Record<TaskStatus, string> = {
    open: 'К работе', in_progress: 'В работе', review: 'На проверке', done: 'Выполнена', not_done: 'Не выполнена',
};
export function taskDisplayColor(task: TaskItem): string {
    if (!task.dutyShiftId && task.dueAt && new Date(task.dueAt).getTime() < Date.now() && !['done', 'not_done'].includes(task.status)) return '#e55363';
    if (task.status === 'done') return '#1faf79';
    if (task.status === 'not_done') return '#8993a4';
    if (task.dutyShiftId) return '#8a63ea';
    if (task.status === 'review') return '#ed9a32';
    if (task.status === 'in_progress') return '#3d70f5';
    return '#718096';
}
export const COLORS = ['#3d70f5', '#1faf79', '#ef5361', '#ed9a32', '#8a63ea', '#4e8cd8', '#16a6b5', '#d65799', '#a98962', '#718096', '#72ae45', '#263248'];
export const COLOR_NAMES = ['Синий', 'Зелёный', 'Красный', 'Оранжевый', 'Фиолетовый', 'Голубой', 'Бирюзовый', 'Розовый', 'Коричневый', 'Серый', 'Салатовый', 'Графитовый'];
export const formatDate = (value: string | null) => value ? new Intl.DateTimeFormat('ru-RU', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' }).format(new Date(value)) : 'Без срока';
export const toLocalInput = (value: string | null) => {
    if (!value) return '';
    const date = new Date(value);
    const local = new Date(date.getTime() - date.getTimezoneOffset() * 60000);
    return local.toISOString().slice(0, 16);
};
export const fromLocalInput = (value: string) => value ? new Date(value).toISOString() : null;
