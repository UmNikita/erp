<script setup lang="ts">
import { computed, ref } from 'vue';
import { useTaskStore } from '../store';
import { STATUS_LABELS, type BoardColumn, type TaskItem, type TaskStatus } from '../types';
import TaskCard from './TaskCard.vue';
const props = defineProps<{ tasks: TaskItem[] }>();
const emit = defineEmits<{ open: [task: TaskItem] }>();
const store = useTaskStore();
const editing = ref(false);
const draft = ref<BoardColumn[]>([]);
const dragging = ref<number | null>(null);
const hovered = ref<TaskStatus | null>(null);
const notice = ref('');
const lanes = computed(() => store.boardColumns.map(column => ({ ...column, tasks: props.tasks.filter(task => task.status === column.status).sort((a, b) => (a.dueAt ?? '9999').localeCompare(b.dueAt ?? '9999')) })));
const overdue = (task: TaskItem) => !task.dutyShiftId && !!task.dueAt && Date.parse(task.dueAt) < Date.now() && !['done', 'not_done'].includes(task.status);
function edit() { draft.value = store.boardColumns.map(column => ({ ...column })); editing.value = true; }
function reorder(index: number, offset: number) {
    const target = index + offset;
    if (target < 0 || target >= draft.value.length) return;
    const [column] = draft.value.splice(index, 1);
    draft.value.splice(target, 0, column);
}
async function save() { if (await store.saveBoardColumns(draft.value)) editing.value = false; }
async function move(task: TaskItem, status: TaskStatus) {
    if (store.saving || task.status === status) return;
    if (overdue(task)) { notice.value = 'Сначала укажите причину просрочки и новый срок в задаче.'; emit('open', task); return; }
    notice.value = '';
    await store.updateTask(task.id, { status });
}
function start(event: DragEvent, task: TaskItem) {
    if (store.saving || overdue(task)) { event.preventDefault(); return; }
    dragging.value = task.id;
    if (event.dataTransfer) { event.dataTransfer.setData('application/x-hub-task', String(task.id)); event.dataTransfer.effectAllowed = 'move'; }
}
function end() { dragging.value = null; hovered.value = null; }
function drop(status: TaskStatus) { const task = props.tasks.find(task => task.id === dragging.value); end(); if (task) void move(task, status); }
</script>
<template>
    <section class="task-board" aria-label="Воронка задач">
        <div class="task-board-toolbar"><span>Переносите задачи между колонками</span><button v-if="store.canAdmin" class="task-btn outline" @click="edit">Настроить воронку</button></div>
        <form v-if="editing" class="task-column-editor" @submit.prevent="save">
            <p>Название, цвет и порядок общие для всех сотрудников. Логика статусов сохраняется.</p>
            <div v-for="(column, index) in draft" :key="column.status" class="task-column-edit-row"><input v-model="column.color" type="color" :aria-label="`Цвет: ${column.name}`" /><input v-model="column.name" required maxlength="60" :aria-label="`Название: ${STATUS_LABELS[column.status]}`" /><small>{{ STATUS_LABELS[column.status] }}</small><button type="button" :disabled="!index" :aria-label="`Поднять ${column.name}`" @click="reorder(index, -1)">↑</button><button type="button" :disabled="index === draft.length - 1" :aria-label="`Опустить ${column.name}`" @click="reorder(index, 1)">↓</button></div>
            <div class="task-column-actions"><button class="task-btn primary" :disabled="store.saving">Сохранить для всех</button><button type="button" class="task-btn outline" @click="editing = false">Отмена</button></div>
        </form>
        <p v-if="notice" class="task-board-notice" role="status">{{ notice }}</p>
        <div class="task-kanban">
            <section v-for="lane in lanes" :key="lane.status" class="task-kanban-lane" :class="{ 'drag-over': hovered === lane.status }" :style="{ '--column-color': lane.color }" @dragover.prevent="hovered = lane.status" @drop.prevent="drop(lane.status)">
                <header><strong>{{ lane.name }}</strong><span>{{ lane.tasks.length }}</span></header>
                <div class="task-kanban-body" tabindex="0" role="region" :aria-label="`Задачи: ${lane.name}`"><article v-for="task in lane.tasks" :key="task.id" class="task-kanban-item" :class="{ dragging: dragging === task.id }" :draggable="!store.saving && !overdue(task)" @dragstart="start($event, task)" @dragend="end">
                    <TaskCard :task="task" hide-status @open="emit('open', task)" />
                </article><div v-if="!lane.tasks.length" class="task-kanban-empty">Нет задач</div></div>
            </section>
        </div>
    </section>
</template>
