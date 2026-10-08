<script setup lang="ts">
import { computed } from 'vue';
import { formatDate, STATUS_LABELS, taskAssignees, taskDisplayColor, type TaskItem } from '../types';

const props = defineProps<{ task: TaskItem; dutyActive?: boolean }>();
const emit = defineEmits<{ open: [] }>();
const overdue = computed(() => !props.task.dutyShiftId && !!props.task.dueAt && new Date(props.task.dueAt).getTime() < Date.now() && !['done', 'not_done'].includes(props.task.status));
</script>

<template>
    <button class="task-card" :class="{ 'duty-active': dutyActive, overdue, completed: ['done', 'not_done'].includes(task.status) }" :style="{ '--card-color': taskDisplayColor(task) }" @click="emit('open')">
        <span class="task-card-top"><span class="task-status">{{ overdue ? 'ЗАДАЧА ПРОСРОЧЕНА' : STATUS_LABELS[task.status] }}</span><span v-if="task.dutyShiftId" class="task-duty-badge">Дежурство</span><span class="task-card-number">#{{ task.id }}</span></span>
        <strong>{{ task.title }}</strong>
        <span class="task-card-bottom" :class="{ late: overdue }">{{ formatDate(task.dueAt) }}<span v-if="taskAssignees(task).length > 1" :title="taskAssignees(task).map((person) => person.name).join(', ')">👥 {{ taskAssignees(task).length }}</span></span>
    </button>
</template>
