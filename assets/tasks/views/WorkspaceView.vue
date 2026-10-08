<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useTaskStore } from '../store';
import { formatDate, isTaskAssignee, type TaskItem } from '../types';
import TaskEditor from '../components/TaskEditor.vue';
import TaskDetails from '../components/TaskDetails.vue';
import TaskCard from '../components/TaskCard.vue';
import WorkspaceInsights from '../components/WorkspaceInsights.vue';

const store = useTaskStore();
const adding = ref(false);
const selectedId = ref<number | null>(null);
const feedbackCollapsed = ref(false);
const now = ref(Date.now());
let clock: ReturnType<typeof setInterval> | undefined;
const feedbackStorageKey = () => `tasks-feedback-collapsed:${store.currentUser?.id ?? 'guest'}`;
onMounted(() => {
    clock = setInterval(() => { now.value = Date.now(); }, 30000);
    try { feedbackCollapsed.value = localStorage.getItem(feedbackStorageKey()) === '1'; } catch { /* Browser storage may be disabled. */ }
});
onUnmounted(() => { if (clock) clearInterval(clock); });
function toggleFeedback() {
    feedbackCollapsed.value = !feedbackCollapsed.value;
    try { localStorage.setItem(feedbackStorageKey(), feedbackCollapsed.value ? '1' : '0'); } catch { /* Panel still works for this visit. */ }
}
const mine = computed(() => store.tasks.filter((task) => isTaskAssignee(task, store.currentUser?.id)));
const isRegularOverdue = (task: TaskItem) => !task.dutyShiftId && !!task.dueAt && new Date(task.dueAt).getTime() < now.value && !['done', 'not_done'].includes(task.status);
const overdueTasks = computed(() => mine.value.filter(isRegularOverdue));
const active = computed(() => mine.value.filter((task) => {
    if (['done', 'not_done'].includes(task.status)) return false;
    if (isRegularOverdue(task)) return false;
    if (!task.dutyShiftId) return true;
    const shift = store.shifts.find((item) => item.id === task.dutyShiftId);
    return !shift || new Date(shift.startsAt).getTime() <= now.value;
}).sort((a, b) => (a.dueAt ?? '9999').localeCompare(b.dueAt ?? '9999')));
const finished = computed(() => mine.value.filter((task) => ['done', 'not_done'].includes(task.status)));
const currentShift = computed(() => store.shifts.find((shift) => shift.assignee.id === store.currentUser?.id && new Date(shift.startsAt).getTime() <= now.value && new Date(shift.endsAt).getTime() > now.value));
const selected = computed(() => store.tasks.find((task) => task.id === selectedId.value) ?? null);
const dutyRemaining = computed(() => active.value.filter((task) => task.dutyShiftId === currentShift.value?.id).length);
const myFeedback = computed(() => store.feedback.filter((item) => item.recipient.id === store.currentUser?.id));
const feedbackDate = (value: string) => new Intl.DateTimeFormat('ru-RU', { day: 'numeric', month: 'short' }).format(new Date(value));
</script>

<template>
    <div class="task-page task-workspace" :class="{ 'with-duty': currentShift }">
        <div class="task-toolbar"><div class="task-summary"><div><strong>{{ active.length }}</strong><span>В работе</span></div><div><strong>{{ finished.filter(t => t.status === 'done').length }}</strong><span>Выполнено</span></div><div><strong class="red">{{ overdueTasks.length }}</strong><span>Просрочено</span></div></div><button class="task-btn primary" @click="adding = !adding; selectedId = null">{{ adding ? 'Закрыть' : '+ Добавить задачу' }}</button></div>
        <div class="task-work-area" :class="{ 'feedback-collapsed': feedbackCollapsed }">
            <div class="task-work-list">
                <TaskEditor v-if="adding" @close="adding = false" />
                <div class="task-board-columns" :class="{ 'has-overdue': overdueTasks.length }"><div class="task-board-main"><div class="task-section-head"><strong>Мои задачи</strong><span>{{ active.length }}</span></div>
                    <div v-if="!active.length" class="task-empty">Пока нет активных задач. Новую задачу можно добавить кнопкой выше.</div>
                    <div class="task-card-grid"><TaskCard v-for="task in active" :key="task.id" :task="task" :duty-active="!!currentShift && task.dutyShiftId === currentShift.id" @open="selectedId = task.id; adding = false" /></div></div>
                    <div v-if="overdueTasks.length" class="task-board-overdue"><div class="task-section-head"><strong>Просроченные задачи</strong><span>{{ overdueTasks.length }}</span></div><p>Укажите причину и назначьте новый срок.</p><div class="task-card-grid"><TaskCard v-for="task in overdueTasks" :key="task.id" :task="task" @open="selectedId = task.id; adding = false" /></div></div></div>
                <template v-if="finished.length"><div class="task-section-head secondary"><strong>Завершённые</strong><span>{{ finished.length }}</span></div><div class="task-card-grid"><TaskCard v-for="task in finished.slice(0, 8)" :key="task.id" :task="task" @open="selectedId = task.id" /></div></template>
                <WorkspaceInsights />
            </div>
            <aside class="task-work-feedback" :class="{ collapsed: feedbackCollapsed }" aria-label="Комментарии руководителя"><button type="button" class="task-feedback-toggle" :aria-expanded="!feedbackCollapsed" :aria-label="feedbackCollapsed ? 'Показать комментарии руководителя' : 'Скрыть комментарии руководителя'" :title="feedbackCollapsed ? 'Показать комментарии' : 'Скрыть комментарии'" @click="toggleFeedback">{{ feedbackCollapsed ? '‹' : '›' }}</button><div class="task-feedback-content" :aria-hidden="feedbackCollapsed"><div class="task-insight-head"><div><strong>От руководителя</strong><span>Комментарии к вашей работе</span></div></div><div class="task-work-feedback-scroll"><div v-if="!myFeedback.length" class="task-feedback-empty">Пока комментариев нет. Здесь появятся сообщения и рекомендации руководителя.</div><article v-for="item in myFeedback" :key="item.id" class="task-feedback-entry"><div><strong>{{ item.author?.name ?? 'Руководитель' }}</strong><time>{{ feedbackDate(item.createdAt) }}</time></div><p>{{ item.body }}</p></article></div></div></aside>
        </div>
        <TaskDetails v-if="selected" :key="selected.id" :task="selected" @close="selectedId = null" />
        <div v-if="currentShift" class="task-duty-bar"><span class="task-duty-dot"></span><div><strong>Вы сейчас на дежурстве · {{ currentShift.title }}</strong><span>{{ formatDate(currentShift.startsAt) }} — {{ formatDate(currentShift.endsAt) }} · {{ dutyRemaining }} задач осталось</span></div><router-link to="/tasks/duties" class="task-btn outline">Открыть дежурство</router-link></div>
    </div>
</template>
