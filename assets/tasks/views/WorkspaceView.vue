<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useTaskStore } from '../store';
import { formatDate, isTaskAssignee, taskAssignees } from '../types';
import TaskEditor from '../components/TaskEditor.vue';
import TaskDetails from '../components/TaskDetails.vue';
import TaskBoard from '../components/TaskBoard.vue';
import WorkspaceInsights from '../components/WorkspaceInsights.vue';
const store = useTaskStore();
const adding = ref(false);
const scope = ref<'personal' | 'shared'>('personal');
const selectedId = ref<number | null>(null);
const now = ref(Date.now());
let clock: ReturnType<typeof setInterval> | undefined;
onMounted(() => { clock = setInterval(() => { now.value = Date.now(); }, 30000); });
onUnmounted(() => { if (clock) clearInterval(clock); });
const mine = computed(() => store.tasks.filter(task => isTaskAssignee(task, store.currentUser?.id)));
const overdueCount = computed(() => mine.value.filter(task => !task.dutyShiftId && task.dueAt && Date.parse(task.dueAt) < now.value && !['done', 'not_done'].includes(task.status)).length);
const boardTasks = computed(() => store.tasks.filter(task => {
    if (scope.value === 'personal' ? (!isTaskAssignee(task, store.currentUser?.id) || taskAssignees(task).length > 1) : taskAssignees(task).length < 2) return false;
    if (!task.dutyShiftId || ['done', 'not_done'].includes(task.status)) return true;
    const shift = store.shifts.find(item => item.id === task.dutyShiftId);
    return !shift || Date.parse(shift.startsAt) <= now.value;
}));
const selected = computed(() => store.tasks.find(task => task.id === selectedId.value) ?? null);
const currentShift = computed(() => store.shifts.find(shift => shift.assignee.id === store.currentUser?.id && Date.parse(shift.startsAt) <= now.value && Date.parse(shift.endsAt) > now.value));
const dutyRemaining = computed(() => mine.value.filter(task => task.dutyShiftId === currentShift.value?.id && !['done', 'not_done'].includes(task.status)).length);
</script>
<template>
    <div class="task-page task-workspace" :class="{ 'with-duty': currentShift }">
        <div class="task-toolbar"><div><h1 class="task-page-title">Рабочий стол</h1><span v-if="overdueCount" class="red">Просрочено: {{ overdueCount }}</span></div><button class="task-btn primary" @click="adding = !adding; selectedId = null">{{ adding ? 'Закрыть' : '+ Добавить задачу' }}</button></div>
        <div class="task-work-list"><TaskEditor v-if="adding" @close="adding = false" />
            <section class="task-board-panel" aria-label="Задачи рабочего стола">
                <div class="task-segmented task-board-scope"><button :class="{ active: scope === 'personal' }" @click="scope = 'personal'">Личные задачи</button><button :class="{ active: scope === 'shared' }" @click="scope = 'shared'">Общие задачи</button></div>
                <TaskBoard :tasks="boardTasks" @open="selectedId = $event.id; adding = false" />
            </section>
            <WorkspaceInsights />
        </div>
        <TaskDetails v-if="selected" :key="selected.id" :task="selected" @close="selectedId = null" />
        <div v-if="currentShift" class="task-duty-bar"><span class="task-duty-dot"></span><div><strong>Вы сейчас на дежурстве · {{ currentShift.title }}</strong><span>{{ formatDate(currentShift.startsAt) }} — {{ formatDate(currentShift.endsAt) }} · {{ dutyRemaining }} задач осталось</span></div><router-link to="/tasks/duties" class="task-btn outline">Открыть дежурство</router-link></div>
    </div>
</template>
