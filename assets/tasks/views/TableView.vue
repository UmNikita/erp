<script setup lang="ts">
import { computed, ref } from 'vue';
import { useTaskStore } from '../store';
import { STATUS_LABELS, isTaskAssignee, taskAssignees, type TaskItem, type TaskStatus } from '../types';
import TaskDetails from '../components/TaskDetails.vue';
import DeadlinePicker from '../components/DeadlinePicker.vue';
import TaskAssigneesSelect from '../components/TaskAssigneesSelect.vue';

const store = useTaskStore();
const mineOnly = ref(true);
const search = ref('');
const status = ref('all');
const selectedId = ref<number | null>(null);
const selected = computed(() => store.tasks.find((task) => task.id === selectedId.value) ?? null);
const visible = computed(() => store.tasks.filter((task) => {
    if (mineOnly.value && !isTaskAssignee(task, store.currentUser?.id)) return false;
    if (status.value !== 'all' && task.status !== status.value) return false;
    const query = search.value.trim().toLocaleLowerCase('ru-RU');
    return !query || `${task.title} ${task.description ?? ''} ${taskAssignees(task).map((person) => person.name).join(' ')}`.toLocaleLowerCase('ru-RU').includes(query);
}));
function update(task: TaskItem, field: string, value: unknown) { return store.updateTask(task.id, { [field]: value }); }
function isOverdue(task: TaskItem): boolean { return !task.dutyShiftId && !!task.dueAt && new Date(task.dueAt).getTime() < Date.now() && !['done', 'not_done'].includes(task.status); }
function updateText(task: TaskItem, field: 'title' | 'description', event: Event) {
    const value = (event.target as HTMLInputElement).value.trim();
    if (value && value !== (task[field] ?? '')) update(task, field, value);
}
</script>

<template>
    <div class="task-page task-table-page">
        <div class="task-toolbar table"><div class="task-filter"><input v-model="search" placeholder="Поиск по задачам" aria-label="Поиск" /><select v-model="status" aria-label="Фильтр статуса"><option value="all">Все статусы</option><option v-for="(label, key) in STATUS_LABELS" :key="key" :value="key">{{ label }}</option></select><label class="task-check"><input v-model="mineOnly" type="checkbox" /> Только мои</label></div><span class="task-count">Показано: {{ visible.length }}</span></div>
        <div class="task-table-layout">
            <div class="task-table-scroll"><table class="task-table"><thead><tr><th>Задача</th><th>Ответственный</th><th>Дедлайн</th><th>Статус</th><th>Приоритет</th><th>Поставил</th><th>Источник</th><th></th></tr></thead><tbody>
                <tr v-for="task in visible" :key="task.id"><td><input class="task-inline-title" :value="task.title" maxlength="180" @blur="updateText(task, 'title', $event)" @keydown.enter="($event.target as HTMLInputElement).blur()" /><small>{{ task.description || 'Без описания' }}</small></td><td><TaskAssigneesSelect :people="store.users" :selected-ids="taskAssignees(task).map((person) => person.id)" :disabled="store.saving || isOverdue(task)" compact @change="update(task, 'assigneeIds', $event)" /></td><td><DeadlinePicker :value="task.dueAt" :compact="true" :disabled="isOverdue(task)" @apply="update(task, 'dueAt', $event)" /></td><td><select :value="task.status" :disabled="isOverdue(task)" @change="update(task, 'status', ($event.target as HTMLSelectElement).value as TaskStatus)"><option v-for="(label, key) in STATUS_LABELS" :key="key" :value="key">{{ label }}</option></select></td><td><select :value="task.priority" @change="update(task, 'priority', ($event.target as HTMLSelectElement).value)"><option value="low">Низкий</option><option value="normal">Обычный</option><option value="high">Высокий</option></select></td><td><select :value="(task.requestedBy ?? task.createdBy)?.id ?? ''" :disabled="!store.canAdmin && task.createdBy?.id !== store.currentUser?.id" @change="update(task, 'requestedById', Number(($event.target as HTMLSelectElement).value))"><option value="" disabled>По расписанию дежурства</option><option v-for="person in store.users" :key="person.id" :value="person.id">{{ person.name }}</option></select></td><td><span v-if="task.dutyShiftId" class="task-duty-tag">Дежурство</span><span v-else class="task-muted">Обычная</span></td><td><button class="task-icon-btn" :aria-label="`Открыть ${task.title}`" @click="selectedId = task.id">↗</button></td></tr>
                <tr v-if="!visible.length"><td colspan="8" class="task-table-empty">По выбранным условиям задач нет</td></tr>
            </tbody></table></div>
        </div>
        <TaskDetails v-if="selected" :key="selected.id" :task="selected" @close="selectedId = null" />
    </div>
</template>
