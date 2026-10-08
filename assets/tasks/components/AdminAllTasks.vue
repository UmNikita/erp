<script setup lang="ts">
import { computed, ref } from 'vue';
import { useTaskStore } from '../store';
import { formatDate, taskAssignees, taskDisplayColor, type TaskStatus } from '../types';
import TaskDetails from './TaskDetails.vue';
import TaskEditor from './TaskEditor.vue';

const store = useTaskStore();
const STATUS_LABELS = computed(() => store.statusLabels);
const search = ref('');
const status = ref<TaskStatus | 'all'>('all');
const personId = ref(0);
const selectedId = ref<number | null>(null);
const creating = ref(false);
const selectedTask = computed(() => store.tasks.find((task) => task.id === selectedId.value) ?? null);
const visible = computed(() => store.tasks.filter((task) => {
    if (status.value !== 'all' && task.status !== status.value) return false;
    if (personId.value && !taskAssignees(task).some((person) => person.id === personId.value)) return false;
    const query = search.value.trim().toLocaleLowerCase('ru-RU');
    return !query || `${task.id} ${task.title} ${task.description ?? ''} ${taskAssignees(task).map((person) => person.name).join(' ')}`.toLocaleLowerCase('ru-RU').includes(query);
}));
</script>

<template>
    <section class="task-admin-all">
        <div class="task-admin-all-head"><div class="task-section-title"><strong>Все задачи</strong><span>Все сотрудники · редактирование и обсуждение в карточке</span></div><button type="button" class="task-btn primary" @click="creating = !creating">{{ creating ? 'Закрыть' : '+ Добавить задачу' }}</button></div>
        <div class="task-admin-all-filters"><input v-model="search" type="search" aria-label="Поиск по всем задачам" placeholder="Поиск по названию, номеру или ответственному" /><select v-model="personId" aria-label="Ответственный"><option :value="0">Все сотрудники</option><option v-for="person in store.users" :key="person.id" :value="person.id">{{ person.name }}</option></select><select v-model="status" aria-label="Статус"><option value="all">Все статусы</option><option v-for="(label, key) in STATUS_LABELS" :key="key" :value="key">{{ label }}</option></select><span>{{ visible.length }} из {{ store.tasks.length }}</span></div>
        <div class="task-admin-all-scroll"><TaskEditor v-if="creating" @close="creating = false" /><div class="task-admin-all-list"><button v-for="task in visible" :key="task.id" type="button" class="task-admin-all-row" :style="{ '--card-color': taskDisplayColor(task) }" @click="selectedId = task.id"><span class="task-admin-all-main"><strong>{{ task.title }}</strong><small>#{{ task.id }} · {{ task.dutyShiftId ? 'Дежурство' : 'Обычная задача' }}</small></span><span class="task-admin-all-owners">{{ taskAssignees(task).map((person) => person.name).join(', ') || 'Не назначены' }}</span><span class="task-admin-all-status">{{ STATUS_LABELS[task.status] }}</span><time>{{ formatDate(task.dueAt) }}</time><span aria-hidden="true">↗</span></button><div v-if="!visible.length" class="task-empty">Задач по выбранным условиям нет</div></div></div>
        <TaskDetails v-if="selectedTask" :key="selectedTask.id" :task="selectedTask" @close="selectedId = null" />
    </section>
</template>
