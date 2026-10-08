<script setup lang="ts">
import { computed, ref } from 'vue';
import { useTaskStore } from '../store';
import { formatDate, STATUS_LABELS, type TaskAudit, type TaskAuditChange } from '../types';
import TaskDetails from './TaskDetails.vue';

const store = useTaskStore();
const opened = ref(false);
const selectedId = ref<number | null>(null);
const selectedTask = computed(() => store.tasks.find((task) => task.id === selectedId.value) ?? null);
const actionLabel: Record<TaskAudit['action'], string> = { created: 'Создана задача', updated: 'Изменена задача', message_added: 'Сообщение в задаче', deadline_moved: 'Перенесён дедлайн' };
const entryWord = computed(() => { const count = store.auditTotal; return count % 10 === 1 && count % 100 !== 11 ? 'запись' : count % 10 >= 2 && count % 10 <= 4 && (count % 100 < 12 || count % 100 > 14) ? 'записи' : 'записей'; });
const fieldLabel: Record<string, string> = { title: 'Название', description: 'Описание', result: 'Результат', status: 'Статус', priority: 'Приоритет', dueAt: 'Дедлайн', assignees: 'Ответственные', requestedBy: 'Поставил' };
function display(value: string | string[] | null | undefined, field: string): string {
    if (Array.isArray(value)) return value.join(', ') || '—';
    if (!value) return '—';
    if (field === 'dueAt') return formatDate(value);
    if (field === 'status') return STATUS_LABELS[value as keyof typeof STATUS_LABELS] ?? value;
    if (field === 'priority') return ({ low: 'Низкий', normal: 'Обычный', high: 'Высокий' } as Record<string, string>)[value] ?? value;
    return value;
}
function changes(entry: TaskAudit): { label: string; value: string }[] {
    return Object.entries(entry.changes).map(([field, raw]) => {
        if (field === 'reason' && typeof raw === 'string') return { label: 'Причина', value: raw };
        const change = raw as TaskAuditChange;
        return { label: fieldLabel[field] ?? field, value: `${display(change.before, field)} → ${display(change.after, field)}` };
    });
}
async function toggle() {
    opened.value = !opened.value;
    if (opened.value && !store.auditLoaded) await store.loadAudit(true);
}
</script>

<template>
    <section class="task-admin-audit"><button type="button" class="task-admin-audit-toggle" :aria-expanded="opened" @click="toggle"><span><strong>Журнал аудита</strong><small>Изменения задач, сообщения и переносы сроков</small></span><span>{{ opened ? 'Скрыть' : 'Показать' }} {{ opened ? '⌃' : '⌄' }}</span></button><div v-if="opened" class="task-admin-audit-body"><div class="task-admin-audit-tools"><span>{{ store.auditTotal }} {{ entryWord }}</span><button type="button" :disabled="store.auditLoading" @click="store.loadAudit(true)">Обновить</button></div><div class="task-admin-audit-list"><article v-for="entry in store.audit" :key="entry.id" class="task-admin-audit-entry"><div class="task-admin-audit-entry-head"><span class="task-admin-audit-action">{{ actionLabel[entry.action] ?? entry.action }}</span><button v-if="entry.taskId && store.tasks.some((task) => task.id === entry.taskId)" type="button" @click="selectedId = entry.taskId">{{ entry.taskTitle }} #{{ entry.taskId }} ↗</button><strong v-else>{{ entry.taskTitle }}</strong><time>{{ formatDate(entry.createdAt) }}</time></div><span class="task-admin-audit-actor">{{ entry.actor?.name ?? 'Система' }}</span><div v-if="changes(entry).length" class="task-admin-audit-changes"><span v-for="(change, index) in changes(entry)" :key="index"><strong>{{ change.label }}:</strong> {{ change.value }}</span></div></article><div v-if="store.auditLoading" class="task-empty">Загружаем записи…</div><div v-else-if="!store.audit.length" class="task-empty">Новых записей пока нет. История начнётся после установки обновления.</div></div><button v-if="store.audit.length < store.auditTotal" type="button" class="task-btn outline task-admin-audit-more" :disabled="store.auditLoading" @click="store.loadAudit()">Показать ещё</button></div></section>
    <TaskDetails v-if="selectedTask" :key="selectedTask.id" :task="selectedTask" @close="selectedId = null" />
</template>
