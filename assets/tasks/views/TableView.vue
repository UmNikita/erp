<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue';
import { useTaskStore } from '../store';
import { taskAssignees, type TaskItem, type TaskStatus } from '../types';
import UserFilter from '../components/UserFilter.vue';
import { filterTasks, type TaskFilters } from '../utils/tableFilters';
import TaskDetails from '../components/TaskDetails.vue';
import DeadlinePicker from '../components/DeadlinePicker.vue';
import TaskAssigneesSelect from '../components/TaskAssigneesSelect.vue';

const store = useTaskStore();
const STATUS_LABELS = computed(() => store.statusLabels);
const filters = reactive<TaskFilters>({ userIds: [], search: '', status: 'all', priority: 'all', source: 'all', requestedBy: 'all', deadline: 'all' });
watch(() => store.currentUser?.id, id => { if (id) filters.userIds = [id]; }, { immediate: true });
function reset() { Object.assign(filters, { userIds: store.currentUser ? [store.currentUser.id] : [], search: '', status: 'all', priority: 'all', source: 'all', requestedBy: 'all', deadline: 'all' }); }
const selectedId = ref<number | null>(null);
const selected = computed(() => store.tasks.find((task) => task.id === selectedId.value) ?? null);
const visible = computed(() => filterTasks(store.tasks, filters));
function update(task: TaskItem, field: string, value: unknown) { return store.updateTask(task.id, { [field]: value }); }
function isOverdue(task: TaskItem): boolean { return !task.dutyShiftId && !!task.dueAt && new Date(task.dueAt).getTime() < Date.now() && !['done', 'not_done'].includes(task.status); }
function updateText(task: TaskItem, field: 'title' | 'description', event: Event) {
    const value = (event.target as HTMLInputElement).value.trim();
    if (value && value !== (task[field] ?? '')) update(task, field, value);
}
</script>

<template>
    <div class="task-page task-table-page">
        <div class="task-toolbar"><h1 class="task-page-title">Рабочая таблица</h1><span class="task-count">Показано: {{ visible.length }}</span></div>
        <div class="task-filter task-filter-expanded"><input v-model="filters.search" placeholder="Поиск по задачам" aria-label="Поиск" /><UserFilter :people="store.users" :selected-ids="filters.userIds" :current-user-id="store.currentUser!.id" @change="filters.userIds = $event" /><select v-model="filters.status" aria-label="Фильтр статуса"><option value="all">Все статусы</option><option v-for="column in store.boardColumns" :key="column.status" :value="column.status">{{ column.name }}</option></select><select v-model="filters.priority" aria-label="Фильтр приоритета"><option value="all">Все приоритеты</option><option value="high">Высокий</option><option value="normal">Обычный</option><option value="low">Низкий</option></select><select v-model="filters.source" aria-label="Источник задачи"><option value="all">Все источники</option><option value="regular">Обычные задачи</option><option value="duty">Дежурства</option></select><select v-model="filters.deadline" aria-label="Фильтр срока"><option value="all">Любой срок</option><option value="overdue">Просроченные</option><option value="today">На сегодня</option><option value="week">Ближайшие 7 дней</option><option value="none">Без срока</option></select><select v-model="filters.requestedBy" aria-label="Постановщик задачи"><option value="all">Все постановщики</option><option v-for="person in store.users" :key="person.id" :value="String(person.id)">{{ person.name }}</option></select><button class="task-btn outline" @click="reset">Сбросить</button></div>
        <div class="task-table-layout">
            <div class="task-table-scroll"><table class="task-table"><thead><tr><th>Задача</th><th>Ответственный</th><th>Дедлайн</th><th>Статус</th><th>Приоритет</th><th>Поставил</th><th>Источник</th><th></th></tr></thead><tbody>
                <tr v-for="task in visible" :key="task.id"><td><input class="task-inline-title" :value="task.title" maxlength="180" @blur="updateText(task, 'title', $event)" @keydown.enter="($event.target as HTMLInputElement).blur()" /><small>{{ task.description || 'Без описания' }}</small></td><td><TaskAssigneesSelect :people="store.users" :selected-ids="taskAssignees(task).map((person) => person.id)" :disabled="store.saving || isOverdue(task)" compact @change="update(task, 'assigneeIds', $event)" /></td><td><DeadlinePicker :value="task.dueAt" :compact="true" :disabled="isOverdue(task)" @apply="update(task, 'dueAt', $event)" /></td><td><select :value="task.status" :disabled="isOverdue(task)" @change="update(task, 'status', ($event.target as HTMLSelectElement).value as TaskStatus)"><option v-for="(label, key) in STATUS_LABELS" :key="key" :value="key">{{ label }}</option></select></td><td><select :value="task.priority" @change="update(task, 'priority', ($event.target as HTMLSelectElement).value)"><option value="low">Низкий</option><option value="normal">Обычный</option><option value="high">Высокий</option></select></td><td><select :value="(task.requestedBy ?? task.createdBy)?.id ?? ''" :disabled="!store.canAdmin && task.createdBy?.id !== store.currentUser?.id" @change="update(task, 'requestedById', Number(($event.target as HTMLSelectElement).value))"><option value="" disabled>По расписанию дежурства</option><option v-for="person in store.users" :key="person.id" :value="person.id">{{ person.name }}</option></select></td><td><span v-if="task.dutyShiftId" class="task-duty-tag">Дежурство</span><span v-else class="task-muted">Обычная</span></td><td><button class="task-icon-btn" :aria-label="`Открыть ${task.title}`" @click="selectedId = task.id">↗</button></td></tr>
                <tr v-if="!visible.length"><td colspan="8" class="task-table-empty">По выбранным условиям задач нет</td></tr>
            </tbody></table></div>
        </div>
        <TaskDetails v-if="selected" :key="selected.id" :task="selected" @close="selectedId = null" />
    </div>
</template>
