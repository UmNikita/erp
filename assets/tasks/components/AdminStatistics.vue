<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import PeriodPicker from '../../accounting/components/PeriodPicker.vue';
import { lastDaysRange } from '../../accounting/utils/dateRange';
import { useTaskStore } from '../store';
import { formatDate, taskAssignees, taskCompletedFor, type TaskItem } from '../types';
import { buildAdminAnalytics, inPeriod, reportOwnerIds, reportOwnerNames } from '../utils/adminAnalytics';
import TaskCard from './TaskCard.vue';
import TaskDetails from './TaskDetails.vue';

const store = useTaskStore();
const initialPeriod = lastDaysRange(30);
const from = ref(initialPeriod.from);
const to = ref(initialPeriod.to);
const selectedIds = ref<number[]>([]);
const initialized = ref(false);
const pickerOpen = ref(false);
const pickerRoot = ref<HTMLElement | null>(null);
const employeeQuery = ref('');
const focusedId = ref<number | null>(null);
const taskId = ref<number | null>(null);
const issueTab = ref<'failed' | 'overdue' | 'transfers'>('failed');
const now = ref(Date.now());
let clock: ReturnType<typeof setInterval> | undefined;

watch(() => store.users, (people) => {
    if (!initialized.value && people.length) { selectedIds.value = people.map((person) => person.id); initialized.value = true; }
    else selectedIds.value = selectedIds.value.filter((id) => people.some((person) => person.id === id));
}, { immediate: true });
watch(selectedIds, (ids) => { if (focusedId.value && !ids.includes(focusedId.value)) focusedId.value = null; });
onMounted(() => { clock = setInterval(() => { now.value = Date.now(); }, 30000); document.addEventListener('mousedown', closePickerOutside); });
onBeforeUnmount(() => { if (clock) clearInterval(clock); document.removeEventListener('mousedown', closePickerOutside); });
function closePickerOutside(event: MouseEvent) { if (!pickerRoot.value?.contains(event.target as Node)) pickerOpen.value = false; }
function togglePerson(id: number) { selectedIds.value = selectedIds.value.includes(id) ? selectedIds.value.filter((value) => value !== id) : [...selectedIds.value, id]; }
function applyPeriod(nextFrom: string, nextTo: string) { from.value = nextFrom; to.value = nextTo; }

const matchingPeople = computed(() => store.users.filter((person) => person.name.toLocaleLowerCase('ru-RU').includes(employeeQuery.value.trim().toLocaleLowerCase('ru-RU'))));
const selectionLabel = computed(() => selectedIds.value.length === store.users.length ? `Все сотрудники · ${store.users.length}` : `Сотрудники · ${selectedIds.value.length} из ${store.users.length}`);
const analytics = computed(() => buildAdminAnalytics(store.tasks, store.overdueReports, store.users, selectedIds.value, from.value, to.value, now.value));
const focused = computed(() => analytics.value.employees.find((item) => item.person.id === focusedId.value) ?? null);
const focusedTasks = computed(() => store.tasks.filter((task) => {
    if (!focusedId.value) return false;
    const completedForPerson = taskCompletedFor(task, focusedId.value) && inPeriod(task.completedAt, from.value, to.value);
    const createdForPerson = taskAssignees(task).some((person) => person.id === focusedId.value) && inPeriod(task.createdAt, from.value, to.value);
    const overdueForPerson = taskAssignees(task).some((person) => person.id === focusedId.value) && task.dueAt && inPeriod(task.dueAt, from.value, to.value)
        && new Date(task.dueAt).getTime() < now.value && !['done', 'not_done'].includes(task.status);
    return completedForPerson || createdForPerson || overdueForPerson;
}).sort((a, b) => (b.completedAt ?? b.createdAt).localeCompare(a.completedAt ?? a.createdAt)));
const focusedReports = computed(() => analytics.value.transferReports.filter((item) => reportOwnerIds(item, store.tasks.find((task) => task.id === item.taskId)).includes(focusedId.value ?? -1)));
const selectedTask = computed(() => store.tasks.find((task) => task.id === taskId.value) ?? null);
const taskOwner = (task: TaskItem) => taskAssignees(task).map((person) => person.name).join(', ') || 'Не назначен';
const completionOwners = (task: TaskItem) => (task.completedForIds?.length ? task.completedForIds : (task.completedFor ? [task.completedFor.id] : []))
    .map((id) => store.users.find((person) => person.id === id)?.name ?? (task.completedFor?.id === id ? task.completedFor.name : null))
    .filter(Boolean).join(', ') || 'Неизвестны';
const percent = (value: number | null) => value == null ? '—' : `${value}%`;
</script>

<template>
    <div class="task-admin-statistics">
        <div class="task-admin-stat-top"><div class="task-section-title"><strong>Статистика команды</strong><span>Сравнение сотрудников, завершённых задач и просрочек</span></div><div class="task-admin-stat-controls">
            <div ref="pickerRoot" class="task-admin-employee-filter"><button class="task-btn outline" type="button" :aria-expanded="pickerOpen" @click="pickerOpen = !pickerOpen">{{ selectionLabel }} <span aria-hidden="true">⌄</span></button><div v-if="pickerOpen" class="task-admin-employee-popover"><input v-model="employeeQuery" type="search" aria-label="Поиск сотрудников для сравнения" placeholder="Поиск сотрудника…" /><div class="task-admin-filter-actions"><button type="button" @click="selectedIds = store.users.map((person) => person.id)">Выбрать всех</button><button type="button" @click="selectedIds = []">Снять выбор</button></div><div class="task-admin-employee-options"><label v-for="person in matchingPeople" :key="person.id"><input type="checkbox" :checked="selectedIds.includes(person.id)" @change="togglePerson(person.id)" /><span>{{ person.name }}</span></label><div v-if="!matchingPeople.length" class="task-employee-no-match">Сотрудник не найден</div></div></div></div>
            <PeriodPicker :from="from" :to="to" @apply="applyPeriod" />
        </div></div>
        <div class="task-admin-stat-scroll">
            <div v-if="!selectedIds.length" class="task-empty">Выберите хотя бы одного сотрудника для отчёта.</div>
            <template v-else>
                <div class="task-admin-kpis"><div><span>Задач поставлено*</span><strong>{{ analytics.totals.assigned }}</strong></div><div><span>Выполнено</span><strong class="green">{{ analytics.totals.done }}</strong></div><div><span>Не выполнено</span><strong class="red">{{ analytics.totals.failed }}</strong></div><div><span>Выполнено в срок**</span><strong>{{ percent(analytics.totals.onTimeRate) }}</strong></div><div><span>Переносов за период</span><strong>{{ analytics.totals.reports }}</strong></div><div><span>Просрочено сейчас***</span><strong class="red">{{ analytics.totals.overdue }}</strong></div></div>
                <section class="task-admin-card"><div class="task-admin-card-head"><div><strong>Сравнение сотрудников</strong><span>Нажмите «Подробнее», чтобы открыть задачи конкретного человека</span></div><span>{{ analytics.employees.length }} чел.</span></div><div class="task-admin-table-wrap"><table class="task-admin-comparison"><thead><tr><th>Сотрудник</th><th>Поставлено*</th><th>Выполнено</th><th>Не выполнено</th><th>Доля выполнения</th><th>В срок**</th><th>Переносов</th><th>Просрочено***</th><th></th></tr></thead><tbody><tr v-for="row in analytics.employees" :key="row.person.id" :class="{ selected: focusedId === row.person.id }"><td><strong>{{ row.person.name }}</strong></td><td>{{ row.assigned }}</td><td class="green">{{ row.done }}</td><td class="red">{{ row.failed }}</td><td><div class="task-admin-rate"><span>{{ percent(row.completionRate) }}</span><i :style="{ width: `${row.completionRate ?? 0}%` }" /></div></td><td>{{ percent(row.onTimeRate) }}</td><td>{{ row.reports }}</td><td class="red">{{ row.overdue }}</td><td><button type="button" @click="focusedId = focusedId === row.person.id ? null : row.person.id">{{ focusedId === row.person.id ? 'Свернуть' : 'Подробнее' }}</button></td></tr></tbody></table></div></section>
                <section v-if="focused" class="task-admin-card task-admin-person"><div class="task-admin-card-head"><div><strong>{{ focused.person.name }}</strong><span>Личная статистика в выбранном периоде</span></div><button type="button" class="task-icon-btn" aria-label="Закрыть статистику сотрудника" @click="focusedId = null">×</button></div><div class="task-admin-person-metrics"><div><strong>{{ focused.done }}</strong><span>выполнено</span></div><div><strong>{{ focused.failed }}</strong><span>не выполнено</span></div><div><strong>{{ percent(focused.completionRate) }}</strong><span>доля выполнения</span></div><div><strong>{{ percent(focused.onTimeRate) }}</strong><span>в срок</span></div><div><strong>{{ focused.reports }}</strong><span>переносов</span></div><div><strong>{{ focused.overdue }}</strong><span>просрочено сейчас</span></div></div><div class="task-admin-person-subhead">Задачи <span>{{ focusedTasks.length }}</span></div><div class="task-card-grid"><TaskCard v-for="task in focusedTasks" :key="task.id" :task="task" @open="taskId = task.id" /><div v-if="!focusedTasks.length" class="task-empty">Задач за период нет</div></div><div v-if="focusedReports.length" class="task-admin-person-subhead">Причины переносов <span>{{ focusedReports.length }}</span></div><div v-for="report in focusedReports" :key="report.id" class="task-admin-person-report"><strong>{{ report.taskTitle }}</strong><span>{{ formatDate(report.createdAt) }}</span><p>{{ report.reason }}</p></div></section>
                <section class="task-admin-card task-admin-issues"><div class="task-admin-card-head"><div><strong>Задачи и просрочки</strong><span>Ответственные и причины переносов по выбранным сотрудникам</span></div></div><div class="task-admin-issue-tabs"><button type="button" :class="{ active: issueTab === 'failed' }" @click="issueTab = 'failed'">Не выполнены <span>{{ analytics.failedTasks.length }}</span></button><button type="button" :class="{ active: issueTab === 'overdue' }" @click="issueTab = 'overdue'">Просрочены сейчас <span>{{ analytics.overdueTasks.length }}</span></button><button type="button" :class="{ active: issueTab === 'transfers' }" @click="issueTab = 'transfers'">История переносов <span>{{ analytics.transferReports.length }}</span></button></div>
                    <div v-if="issueTab === 'failed'" class="task-admin-issue-list"><button v-for="task in analytics.failedTasks" :key="task.id" type="button" class="task-admin-issue-row" @click="taskId = task.id"><span class="task-admin-issue-icon failed">×</span><span><strong>{{ task.title }} <small>#{{ task.id }}</small></strong><small>Ответственные при закрытии: {{ completionOwners(task) }}</small></span><time>{{ formatDate(task.completedAt) }}</time></button><div v-if="!analytics.failedTasks.length" class="task-empty">За этот период невыполненных задач нет</div></div>
                    <div v-if="issueTab === 'overdue'" class="task-admin-issue-list"><button v-for="task in analytics.overdueTasks" :key="task.id" type="button" class="task-admin-issue-row" @click="taskId = task.id"><span class="task-admin-issue-icon overdue">!</span><span><strong>{{ task.title }} <small>#{{ task.id }}</small></strong><small>Ответственные: {{ taskOwner(task) }}{{ task.dutyShiftId ? ' · Дежурство' : '' }}</small></span><time>Дедлайн: {{ formatDate(task.dueAt) }}</time></button><div v-if="!analytics.overdueTasks.length" class="task-empty">Задач с просроченным дедлайном в периоде нет</div></div>
                    <div v-if="issueTab === 'transfers'" class="task-admin-issue-list"><button v-for="report in analytics.transferReports" :key="report.id" type="button" class="task-admin-issue-row transfer" @click="taskId = report.taskId"><span class="task-admin-issue-icon transfer">↗</span><span><strong>{{ report.taskTitle }} <small>#{{ report.taskId }}</small></strong><small>Ответственные в момент переноса: {{ reportOwnerNames(report, store.users, store.tasks.find((task) => task.id === report.taskId)) }} · {{ formatDate(report.createdAt) }}</small><em>{{ report.reason }}</em></span><time>{{ formatDate(report.previousDueAt) }} → {{ formatDate(report.newDueAt) }}</time></button><div v-if="!analytics.transferReports.length" class="task-empty">За этот период переносов нет</div></div>
                </section>
                <p class="task-admin-method">* Задачи, созданные в периоде и назначенные сотруднику сейчас. Общие итоги считают каждую задачу один раз; в строках она учитывается у каждого назначенного. ** Среди закрытых задач с дедлайном: выполнены до него без переноса. *** Ещё открыты, дедлайн пришёлся на выбранный период и уже прошёл. «—» означает, что данных для процента нет.</p>
            </template>
        </div>
        <TaskDetails v-if="selectedTask" :key="selectedTask.id" :task="selectedTask" @close="taskId = null" />
    </div>
</template>
