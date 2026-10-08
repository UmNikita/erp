import { taskAssignees, type TaskItem } from '../types';
export interface TaskFilters { userIds: number[]; search: string; status: string; priority: string; source: string; requestedBy: string; deadline: string }
export function filterTasks(tasks: TaskItem[], filters: TaskFilters, now = Date.now()): TaskItem[] {
    const query = filters.search.trim().toLocaleLowerCase('ru-RU');
    const day = (value: number) => new Intl.DateTimeFormat('en-CA', { timeZone: 'Europe/Moscow', year: 'numeric', month: '2-digit', day: '2-digit' }).format(value);
    return tasks.filter(task => {
        if (!taskAssignees(task).some(person => filters.userIds.includes(person.id))) return false;
        if (filters.status !== 'all' && task.status !== filters.status) return false;
        if (filters.priority !== 'all' && task.priority !== filters.priority) return false;
        if (filters.source === 'duty' && !task.dutyShiftId || filters.source === 'regular' && task.dutyShiftId) return false;
        if (filters.requestedBy !== 'all' && (task.requestedBy ?? task.createdBy)?.id !== Number(filters.requestedBy)) return false;
        const due = task.dueAt ? Date.parse(task.dueAt) : null;
        if (filters.deadline === 'overdue' && (due === null || due >= now || ['done', 'not_done'].includes(task.status) || task.dutyShiftId)) return false;
        if (filters.deadline === 'today' && (due === null || day(due) !== day(now))) return false;
        if (filters.deadline === 'week' && (due === null || due < now || due > now + 7 * 86400000)) return false;
        if (filters.deadline === 'none' && due !== null) return false;
        return !query || `${task.title} ${task.description ?? ''} ${taskAssignees(task).map(person => person.name).join(' ')}`.toLocaleLowerCase('ru-RU').includes(query);
    });
}
