<script setup lang="ts">
import { computed, ref } from 'vue';
import { useTaskStore } from '../store';
import { formatDate, isTaskAssignee, taskCompletedFor, taskDisplayColor } from '../types';
import TaskCard from '../components/TaskCard.vue';
import TaskDetails from '../components/TaskDetails.vue';

const store = useTaskStore();
const STATUS_LABELS = computed(() => store.statusLabels);
const days = ref(30);
const selectedId = ref<number | null>(null);
const mine = computed(() => store.tasks.filter((task) => isTaskAssignee(task, store.currentUser?.id) || taskCompletedFor(task, store.currentUser?.id ?? -1)));
const selected = computed(() => store.tasks.find((task) => task.id === selectedId.value) ?? null);
const currentlyOverdue = computed(() => mine.value.filter((task) => isTaskAssignee(task, store.currentUser?.id) && !task.dutyShiftId && !!task.dueAt && new Date(task.dueAt).getTime() < Date.now() && !['done', 'not_done'].includes(task.status)));
const periodTasks = computed(() => mine.value.filter((task) => !days.value || new Date(task.createdAt).getTime() >= Date.now() - days.value * 86400000));
const reports = computed(() => store.overdueReports.filter((item) => (item.assigneeAtReportIds?.length ? item.assigneeAtReportIds : [(item.assigneeAtReport ?? item.author)?.id]).includes(store.currentUser?.id) && (!days.value || new Date(item.createdAt).getTime() >= Date.now() - days.value * 86400000)));
const complete = computed(() => periodTasks.value.filter((task) => task.status === 'done' && taskCompletedFor(task, store.currentUser?.id ?? -1)).length);
const failed = computed(() => periodTasks.value.filter((task) => task.status === 'not_done' && taskCompletedFor(task, store.currentUser?.id ?? -1)).length);
const rate = computed(() => complete.value + failed.value ? Math.round(complete.value / (complete.value + failed.value) * 100) : 0);
</script>

<template>
    <div class="task-page task-profile-page"><div class="task-toolbar"><h1 class="task-page-title">Личная история</h1><div class="task-segmented"><button v-for="value in [7, 30, 90, 0]" :key="value" :class="{ active: days === value }" @click="days = value">{{ value ? `${value} дней` : 'Всё время' }}</button></div></div>
        <div class="task-profile-scroll"><div class="task-stat-grid"><div class="task-stat"><span>Задач за период</span><strong>{{ periodTasks.length }}</strong></div><div class="task-stat"><span>Выполнено</span><strong class="green">{{ complete }}</strong></div><div class="task-stat"><span>Не выполнено</span><strong class="red">{{ failed }}</strong></div><div class="task-stat"><span>Доля выполненных среди завершённых</span><strong>{{ rate }}%</strong></div><div class="task-stat"><span>Переносов просроченных задач</span><strong class="red">{{ reports.length }}</strong></div></div>
            <template v-if="currentlyOverdue.length"><div class="task-section-head secondary"><strong>Сейчас просрочены</strong><span>{{ currentlyOverdue.length }}</span></div><div class="task-card-grid"><TaskCard v-for="task in currentlyOverdue" :key="task.id" :task="task" @open="selectedId = task.id" /></div></template>
            <div class="task-section-head secondary"><strong>История просрочек и причины</strong><span>{{ reports.length }}</span></div><div class="task-overdue-list"><button v-for="item in reports" :key="item.id" class="task-overdue-entry" @click="selectedId = item.taskId"><div><strong>{{ item.taskTitle }} <small>#{{ item.taskId }}</small></strong><time>{{ formatDate(item.createdAt) }}</time></div><span>{{ formatDate(item.previousDueAt) }} → {{ formatDate(item.newDueAt) }}</span><p>{{ item.reason }}</p></button><div v-if="!reports.length" class="task-empty">За выбранный период переносов нет</div></div>
            <div class="task-section-head secondary"><strong>История задач</strong><span>{{ periodTasks.length }}</span></div><div class="task-profile-history"><div v-for="task in periodTasks" :key="task.id" class="task-history-row"><span class="task-history-color" :style="{ backgroundColor: taskDisplayColor(task) }"></span><strong>{{ task.title }}</strong><span>{{ STATUS_LABELS[task.status] }}</span><time>{{ formatDate(task.createdAt) }}</time></div><div v-if="!periodTasks.length" class="task-empty">В этом периоде задач нет</div></div></div>
        <TaskDetails v-if="selected" :key="selected.id" :task="selected" @close="selectedId = null" />
    </div>
</template>
