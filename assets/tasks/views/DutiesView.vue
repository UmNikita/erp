<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useTaskStore } from '../store';
import { formatDate, type DutySchedule, type DutyShift } from '../types';
import TaskCard from '../components/TaskCard.vue';
import TaskDetails from '../components/TaskDetails.vue';

const store = useTaskStore();
const now = ref(Date.now());
const openedShiftId = ref<number | null>(null);
const selectedTaskId = ref<number | null>(null);
const historyExpanded = ref(false);
let clock: ReturnType<typeof setInterval> | undefined;
onMounted(() => { clock = setInterval(() => { now.value = Date.now(); }, 30000); });
onUnmounted(() => { if (clock) clearInterval(clock); });
const schedules = computed(() => [...store.schedules].sort((a, b) => Number(b.active) - Number(a.active) || a.title.localeCompare(b.title, 'ru')));
const currentShifts = computed(() => store.shifts.filter((shift) => new Date(shift.startsAt).getTime() <= now.value && new Date(shift.endsAt).getTime() > now.value));
const pastShifts = computed(() => [...store.shifts].filter((shift) => new Date(shift.endsAt).getTime() <= now.value).sort((a, b) => b.endsAt.localeCompare(a.endsAt)));
const shownHistory = computed(() => historyExpanded.value ? pastShifts.value : pastShifts.value.slice(0, 6));
const openedShift = computed(() => store.shifts.find((shift) => shift.id === openedShiftId.value) ?? null);
const selectedTask = computed(() => store.tasks.find((task) => task.id === selectedTaskId.value) ?? null);
function shiftTasks(id: number) { return store.tasks.filter((task) => task.dutyShiftId === id); }
function progress(shift: DutyShift) { const tasks = shiftTasks(shift.id); return `${tasks.filter((task) => task.status === 'done').length} / ${tasks.length}`; }
function schedulePeople(schedule: DutySchedule) { return new Set(schedule.days.flatMap((day) => day.enabled ? day.userIds : [])).size; }
function weekdays(schedule: DutySchedule) { return schedule.days.map((day, index) => day.enabled ? ['ПН', 'ВТ', 'СР', 'ЧТ', 'ПТ', 'СБ', 'ВС'][index] : null).filter(Boolean).join(' · ') || 'Дни не выбраны'; }
</script>

<template>
    <div class="task-page task-duties-page">
        <div class="task-toolbar"><div class="task-section-title"><strong>Дежурства</strong><span>Расписание и история смен</span></div><router-link v-if="store.canAdmin" to="/tasks/duties/new" class="task-btn primary">+ Создать дежурство</router-link></div>
        <div class="task-duties-scroll">
            <div class="task-section-head"><strong>Расписания</strong><span>{{ schedules.length }}</span></div>
            <div v-if="!schedules.length" class="task-empty">Расписаний пока нет. Создайте дежурство, выберите дни, время, сотрудников и задачи.</div>
            <div class="task-schedule-grid"><router-link v-for="schedule in schedules" :key="schedule.id" :to="`/tasks/duties/${schedule.id}`" class="task-schedule-card" :class="{ paused: !schedule.active }" :style="{ '--schedule-color': schedule.color }"><div class="task-schedule-top"><span class="task-shift-state">{{ schedule.active ? '● По расписанию' : '○ На паузе' }}</span><span>↗</span></div><strong>{{ schedule.title }}</strong><span class="task-schedule-days">{{ weekdays(schedule) }}</span><div class="task-schedule-bottom"><span>{{ schedulePeople(schedule) }} сотрудников</span><span>{{ schedule.taskTemplates.length }} задач на смену</span></div></router-link></div>
            <template v-if="currentShifts.length"><div class="task-section-head secondary"><strong>Сейчас на дежурстве</strong><span>{{ currentShifts.length }}</span></div><div class="task-shift-grid"><button v-for="shift in currentShifts" :key="shift.id" class="task-shift-card current" :style="{ '--schedule-color': shift.color }" @click="openedShiftId = shift.id"><span class="task-shift-state">● Сейчас</span><h3>{{ shift.title }}</h3><span>{{ shift.assignee.name }}</span><p class="task-shift-time">{{ formatDate(shift.startsAt) }} — {{ formatDate(shift.endsAt) }}</p><div class="task-shift-progress"><span>Выполнено задач</span><strong>{{ progress(shift) }}</strong></div></button></div></template>
            <div class="task-section-head secondary"><strong>Прошлые дежурства</strong><span>{{ pastShifts.length }}</span></div>
            <div v-if="!pastShifts.length" class="task-empty">Прошедших смен пока нет. Здесь появится история с результатами и задачами.</div>
            <div class="task-shift-grid"><button v-for="shift in shownHistory" :key="shift.id" class="task-shift-card past" :style="{ '--schedule-color': shift.color }" @click="openedShiftId = shift.id"><span class="task-shift-state">Завершено</span><h3>{{ shift.title }}</h3><span>{{ shift.assignee.name }}</span><p class="task-shift-time">{{ formatDate(shift.startsAt) }} — {{ formatDate(shift.endsAt) }}</p><div class="task-shift-progress"><span>Выполнено задач</span><strong>{{ progress(shift) }}</strong></div></button></div>
            <button v-if="pastShifts.length > 6" class="task-btn outline task-history-more" @click="historyExpanded = !historyExpanded">{{ historyExpanded ? 'Свернуть историю' : `Показать все · ${pastShifts.length}` }}</button>
        </div>
        <div v-if="openedShift" class="task-modal-backdrop task-shift-backdrop" @click.self="openedShiftId = null"><section class="task-shift-modal" role="dialog" aria-modal="true" :aria-label="`Дежурство ${openedShift.title}`"><div class="task-details-head"><div><span class="task-mini-label">{{ new Date(openedShift.endsAt).getTime() <= now ? 'ПРОШЛОЕ ДЕЖУРСТВО' : 'ТЕКУЩЕЕ ДЕЖУРСТВО' }}</span><h2>{{ openedShift.title }}</h2></div><button class="task-icon-btn" @click="openedShiftId = null" aria-label="Закрыть">×</button></div><div class="task-shift-modal-summary"><span>{{ openedShift.assignee.name }}</span><span>{{ formatDate(openedShift.startsAt) }} — {{ formatDate(openedShift.endsAt) }}</span><strong>Выполнено {{ progress(openedShift) }}</strong></div><p v-if="openedShift.comment" class="task-shift-comment">{{ openedShift.comment }}</p><div class="task-section-head"><strong>Задачи смены</strong><span>{{ shiftTasks(openedShift.id).length }}</span></div><div class="task-card-grid"><TaskCard v-for="task in shiftTasks(openedShift.id)" :key="task.id" :task="task" @open="selectedTaskId = task.id" /></div><div v-if="!shiftTasks(openedShift.id).length" class="task-empty">Задач не было</div></section></div>
        <TaskDetails v-if="selectedTask" :key="selectedTask.id" :task="selectedTask" @close="selectedTaskId = null" />
    </div>
</template>
