<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useTaskStore } from '../store';
import { COLORS, type DutyDay, type DutySchedule, type DutyTaskTemplate, type TaskPriority } from '../types';

const store = useTaskStore();
const route = useRoute();
const router = useRouter();
const labels = ['ПН', 'ВТ', 'СР', 'ЧТ', 'ПТ', 'СБ', 'ВС'];
const personSearch = ref('');
const draggingId = ref<number | null>(null);
const pickedId = ref<number | null>(null);
const editingTaskId = ref<string | null>(null);
const addingTask = ref(false);
const localError = ref('');
const taskDraft = reactive({ title: '', description: '', priority: 'normal' as TaskPriority });
const form = reactive({ title: '', color: '#8a63ea', comment: '', active: true, sameTime: true, start: '07:00', end: '18:00', days: [] as DutyDay[], taskTemplates: [] as DutyTaskTemplate[] });
const scheduleId = computed(() => !route.params.id || route.params.id === 'new' ? null : Number(route.params.id));
const existing = computed(() => store.schedules.find((item) => item.id === scheduleId.value) ?? null);
const filteredPeople = computed(() => store.users.filter((person) => person.name.toLocaleLowerCase('ru-RU').includes(personSearch.value.trim().toLocaleLowerCase('ru-RU'))));
const assignedCount = computed(() => form.days.reduce((sum, day) => sum + (day.enabled ? day.userIds.length : 0), 0));

function fill(schedule: DutySchedule | null) {
    form.title = schedule?.title ?? '';
    form.color = schedule?.color ?? '#8a63ea';
    form.comment = schedule?.comment ?? '';
    form.active = schedule?.active ?? true;
    form.sameTime = schedule?.sameTime ?? true;
    form.days = schedule?.days?.length === 7 ? schedule.days.map((day) => ({ ...day, userIds: [...day.userIds] })) : labels.map((_, index) => ({ enabled: index < 5, start: '07:00', end: '18:00', userIds: [] }));
    form.start = form.days[0]?.start ?? '07:00';
    form.end = form.days[0]?.end ?? '18:00';
    form.taskTemplates = (schedule?.taskTemplates ?? []).map((item) => ({ ...item }));
    editingTaskId.value = null; addingTask.value = false; localError.value = '';
}
watch(existing, fill, { immediate: true });
watch(() => [form.start, form.end, form.sameTime], () => {
    if (form.sameTime) for (const day of form.days) { day.start = form.start; day.end = form.end; }
});
function personName(id: number) { return store.users.find((person) => person.id === id)?.name ?? `Сотрудник #${id}`; }
function assign(dayIndex: number, id: number | null) {
    if (!id) return;
    const day = form.days[dayIndex];
    day.enabled = true;
    if (!day.userIds.includes(id)) day.userIds.push(id);
    pickedId.value = null; draggingId.value = null;
}
function drop(dayIndex: number, event: DragEvent) {
    event.preventDefault();
    const id = Number(event.dataTransfer?.getData('text/plain') || draggingId.value);
    assign(dayIndex, id);
}
function startDrag(id: number, event: DragEvent) {
    draggingId.value = id;
    event.dataTransfer?.setData('text/plain', String(id));
    if (event.dataTransfer) event.dataTransfer.effectAllowed = 'copy';
}
function startTask(template?: DutyTaskTemplate) {
    addingTask.value = !template;
    editingTaskId.value = template?.id ?? null;
    taskDraft.title = template?.title ?? '';
    taskDraft.description = template?.description ?? '';
    taskDraft.priority = template?.priority ?? 'normal';
}
function saveTask() {
    const title = taskDraft.title.trim();
    if (!title) return;
    if (editingTaskId.value) {
        const item = form.taskTemplates.find((task) => task.id === editingTaskId.value);
        if (item) Object.assign(item, { title, description: taskDraft.description.trim(), priority: taskDraft.priority });
    } else form.taskTemplates.push({ id: globalThis.crypto?.randomUUID?.() ?? String(Date.now()), title, description: taskDraft.description.trim(), priority: taskDraft.priority });
    editingTaskId.value = null; addingTask.value = false;
}
async function save() {
    localError.value = '';
    if (!form.title.trim()) { localError.value = 'Назовите дежурство'; return; }
    if (form.active && !assignedCount.value) { localError.value = 'Назначьте хотя бы одного человека на выбранный день или выключите расписание'; return; }
    if (form.days.some((day) => day.start === day.end)) { localError.value = 'Время начала и окончания не должно совпадать'; return; }
    const payload = { title: form.title.trim(), color: form.color, comment: form.comment.trim(), active: form.active, sameTime: form.sameTime, days: form.days, taskTemplates: form.taskTemplates };
    const ok = scheduleId.value ? await store.updateSchedule(scheduleId.value, payload) : await store.createSchedule(payload);
    if (ok) {
        const id = scheduleId.value ?? Math.max(...store.schedules.map((item) => item.id));
        router.push(`/tasks/duties/${id}`);
    }
}
</script>

<template>
    <div class="task-page task-duty-detail-page">
        <div class="task-toolbar"><div class="task-duty-heading"><router-link to="/tasks/duties" class="task-btn outline">← Назад</router-link><div><strong>{{ scheduleId ? 'Настройки дежурства' : 'Новое дежурство' }}</strong><span>Расписание по московскому времени · изменения действуют для будущих смен</span></div></div><button v-if="store.canAdmin" class="task-btn primary" :disabled="store.saving" @click="save">{{ store.saving ? 'Сохраняем…' : 'Сохранить дежурство' }}</button></div>
        <div v-if="scheduleId && !existing && !store.loading" class="task-empty">Дежурство не найдено</div>
        <div v-else class="task-duty-detail-scroll">
            <div v-if="localError" class="task-local-error">{{ localError }}</div>
            <section class="task-duty-section"><div class="task-duty-section-head"><strong>О дежурстве</strong><label class="task-switch"><input v-model="form.active" type="checkbox" :disabled="!store.canAdmin" /><span>{{ form.active ? 'Расписание активно' : 'На паузе' }}</span></label></div><div class="task-duty-meta"><label class="task-field"><span>Название</span><input v-model="form.title" maxlength="160" placeholder="Например, входящие заявки" :disabled="!store.canAdmin" /></label><label class="task-field"><span>Комментарий для дежурных</span><textarea v-model="form.comment" rows="2" maxlength="3000" placeholder="Что важно знать об этой смене" :disabled="!store.canAdmin" /></label></div><div class="task-field"><span>Цвет дежурства</span><div class="task-swatches"><button v-for="color in COLORS" :key="color" type="button" :style="{ backgroundColor: color }" :class="{ selected: form.color === color }" :disabled="!store.canAdmin" :aria-label="`Цвет ${color}`" @click="form.color = color"></button></div></div></section>
            <section class="task-duty-section"><div class="task-duty-section-head"><div><strong>Дни и время</strong><p>Выберите дни. Смены, переходящие через полночь, закончатся на следующий день.</p></div><div class="task-segmented"><button :class="{ active: form.sameTime }" :disabled="!store.canAdmin" @click="form.sameTime = true">Одинаковое время</button><button :class="{ active: !form.sameTime }" :disabled="!store.canAdmin" @click="form.sameTime = false">Для каждого дня</button></div></div><div class="task-weekdays"><button v-for="(day, index) in form.days" :key="index" class="task-weekday" :class="{ active: day.enabled, pulsing: draggingId || pickedId, assigned: day.userIds.length }" :disabled="!store.canAdmin" @click="form.days[index].enabled = !day.enabled">{{ labels[index] }}</button></div><div v-if="form.sameTime" class="task-time-common"><label class="task-field"><span>С</span><input v-model="form.start" type="time" :disabled="!store.canAdmin" /></label><label class="task-field"><span>До</span><input v-model="form.end" type="time" :disabled="!store.canAdmin" /></label><span>МСК · для всех выбранных дней</span></div><div v-else class="task-day-times"><div v-for="(day, index) in form.days" :key="index" v-show="day.enabled"><strong>{{ labels[index] }}</strong><label class="task-field"><span>С</span><input v-model="day.start" type="time" :disabled="!store.canAdmin" /></label><label class="task-field"><span>До</span><input v-model="day.end" type="time" :disabled="!store.canAdmin" /></label></div></div></section>
            <section class="task-duty-section"><div class="task-duty-section-head"><div><strong>Ответственные по дням</strong><p>Перетащите сотрудника на день или нажмите на сотрудника, затем на нужный день. Можно назначить нескольких.</p></div></div><div class="task-assignment-grid"><div v-for="(day, index) in form.days" :key="index" class="task-assignment-day" :class="{ disabled: !day.enabled, pulsing: draggingId || pickedId }" @dragover.prevent @drop="drop(index, $event)" @click="pickedId && assign(index, pickedId)"><div><strong>{{ labels[index] }}</strong><span>{{ day.enabled ? `${day.start}–${day.end}` : 'Выходной' }}</span></div><div class="task-assigned-people"><span v-for="id in day.userIds" :key="id" class="task-person-chip">{{ personName(id) }} <button v-if="store.canAdmin" :aria-label="`Убрать ${personName(id)}`" @click.stop="day.userIds = day.userIds.filter((value) => value !== id)">×</button></span><span v-if="!day.userIds.length" class="task-muted">Никого нет</span></div></div></div><div class="task-people-pool"><div class="task-people-head"><strong>Сотрудники</strong><input v-model="personSearch" placeholder="Поиск сотрудника" aria-label="Поиск сотрудника" /></div><div class="task-people-list"><button v-for="person in filteredPeople" :key="person.id" class="task-person-source" :class="{ picked: pickedId === person.id }" :draggable="store.canAdmin" @dragstart="startDrag(person.id, $event)" @dragend="draggingId = null" @click="pickedId = pickedId === person.id ? null : person.id"><span class="task-avatar">{{ person.name.slice(0, 1) }}</span>{{ person.name }}</button><span v-if="!filteredPeople.length" class="task-muted">Никого не найдено</span></div></div></section>
            <section class="task-duty-section"><div class="task-duty-section-head"><div><strong>Задачи дежурства <span class="task-duty-count">{{ form.taskTemplates.length }}</span></strong><p>Каждый назначенный сотрудник получит собственную копию этих задач во время своей смены.</p></div><button v-if="store.canAdmin" class="task-btn outline" @click="startTask()">+ Добавить задачу</button></div><div class="task-card-grid task-template-grid"><button v-for="item in form.taskTemplates" :key="item.id" class="task-card task-template-card" :style="{ '--card-color': form.color }" @click="startTask(item)"><span class="task-card-top"><span class="task-status">Дежурство</span><span class="task-card-number">{{ item.priority === 'high' ? 'Важная' : 'Шаблон' }}</span></span><strong>{{ item.title }}</strong><span class="task-card-bottom">{{ item.description || 'Без описания' }}</span></button></div><div v-if="!form.taskTemplates.length" class="task-empty task-duty-empty">Задач пока нет. Добавьте то, что должен сделать дежурный.</div><form v-if="(addingTask || editingTaskId) && store.canAdmin" class="task-editor task-template-editor" @submit.prevent="saveTask"><div class="task-editor-head"><strong>{{ editingTaskId ? 'Изменить задачу' : 'Новая задача дежурства' }}</strong><button type="button" class="task-icon-btn" @click="addingTask = false; editingTaskId = null">×</button></div><label class="task-field"><span>Название</span><input v-model="taskDraft.title" required maxlength="180" placeholder="Что нужно сделать?" /></label><label class="task-field"><span>Описание</span><textarea v-model="taskDraft.description" maxlength="3000" rows="2" placeholder="Ожидаемый результат" /></label><label class="task-field"><span>Приоритет</span><select v-model="taskDraft.priority"><option value="low">Низкий</option><option value="normal">Обычный</option><option value="high">Высокий</option></select></label><div class="task-actions"><button v-if="editingTaskId" type="button" class="task-btn ghost task-danger" @click="form.taskTemplates = form.taskTemplates.filter((item) => item.id !== editingTaskId); editingTaskId = null">Удалить</button><button class="task-btn primary">{{ editingTaskId ? 'Сохранить задачу' : 'Добавить задачу' }}</button></div></form></section>
            <div class="task-duty-footer"><span>{{ scheduleId ? 'Изменения расписания начнут действовать со следующего дня. Прошлые смены сохранятся.' : 'Первая смена появится в выбранный день автоматически.' }}</span><button v-if="store.canAdmin" class="task-btn primary" :disabled="store.saving" @click="save">Сохранить дежурство</button></div>
        </div>
    </div>
</template>
