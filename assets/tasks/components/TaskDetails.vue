<script setup lang="ts">
import { computed, nextTick, onMounted, onUnmounted, ref } from 'vue';
import { useTaskStore } from '../store';
import DeadlinePicker from './DeadlinePicker.vue';
import TaskAssigneesSelect from './TaskAssigneesSelect.vue';
import { formatDate, fromLocalInput, STATUS_LABELS, taskAssignees, taskDisplayColor, toLocalInput, type TaskItem, type TaskStatus } from '../types';

const props = defineProps<{ task: TaskItem }>();
const emit = defineEmits<{ close: [] }>();
const store = useTaskStore();
type EditableField = 'title' | 'description' | 'result';
const editingField = ref<EditableField | null>(null);
const draft = ref('');
const detailsElement = ref<HTMLElement | null>(null);
const draftMessage = ref('');
const messages = computed(() => store.messages.filter((message) => message.taskId === props.task.id));
const shift = computed(() => store.shifts.find((item) => item.id === props.task.dutyShiftId));
const canEditRequester = computed(() => store.canAdmin || props.task.createdBy?.id === store.currentUser?.id);
const overdue = computed(() => !props.task.dutyShiftId && !!props.task.dueAt && new Date(props.task.dueAt).getTime() < Date.now() && !['done', 'not_done'].includes(props.task.status));
const previousReports = computed(() => store.overdueReports.filter((item) => item.taskId === props.task.id));
const reason = ref('');
const overdueError = ref('');
const proposed = new Date(Date.now() + 86400000); proposed.setHours(18, 0, 0, 0);
const nextDueAt = ref(toLocalInput(proposed.toISOString()));
const messageTime = (value: string) => new Intl.DateTimeFormat('ru-RU', { hour: '2-digit', minute: '2-digit' }).format(new Date(value));
function onKey(event: KeyboardEvent) {
    if (event.key !== 'Escape') return;
    if (editingField.value) { editingField.value = null; event.stopPropagation(); }
    else emit('close');
}
onMounted(() => window.addEventListener('keydown', onKey));
onUnmounted(() => window.removeEventListener('keydown', onKey));
async function beginEdit(field: EditableField) {
    if (store.saving) return;
    editingField.value = field;
    draft.value = String(props.task[field] ?? '');
    await nextTick();
    detailsElement.value?.querySelector<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>('.task-inline-editor')?.focus();
}
async function commitEdit() {
    const field = editingField.value;
    if (!field || store.saving) return;
    const text = draft.value.trim();
    if (field === 'title' && !text) { store.error = 'Название задачи не может быть пустым'; return; }
    if (text === (props.task[field] ?? '')) { editingField.value = null; return; }
    if (await store.updateTask(props.task.id, { [field]: text })) editingField.value = null;
}
async function changeChoice(field: 'requestedById' | 'priority', event: Event) {
    const select = event.target as HTMLSelectElement;
    const value = field === 'priority' ? select.value : Number(select.value);
    const previous = field === 'requestedById' ? (props.task.requestedBy ?? props.task.createdBy)?.id : props.task.priority;
    if (value === previous) return;
    if (!await store.updateTask(props.task.id, { [field]: value })) select.value = String(previous ?? '');
}
async function changeAssignees(ids: number[]) { await store.updateTask(props.task.id, { assigneeIds: ids }); }
async function changeDeadline(value: string | null) {
    if (overdue.value || value === props.task.dueAt) return;
    await store.updateTask(props.task.id, { dueAt: value });
}
async function changeStatus(event: Event) {
    const select = event.target as HTMLSelectElement;
    const status = select.value as TaskStatus;
    if (status === props.task.status) return;
    if (!await store.updateTask(props.task.id, { status })) select.value = props.task.status;
}
async function send() {
    if (!draftMessage.value.trim()) return;
    if (await store.addMessage(props.task.id, draftMessage.value.trim())) draftMessage.value = '';
}
async function reportOverdue() {
    const iso = fromLocalInput(nextDueAt.value);
    if (!reason.value.trim()) { overdueError.value = 'Укажите причину'; return; }
    if (!iso || new Date(iso).getTime() <= Date.now()) { overdueError.value = 'Новый срок должен быть в будущем'; return; }
    overdueError.value = '';
    if (await store.reportOverdue(props.task.id, reason.value.trim(), iso)) reason.value = '';
}
</script>

<template>
    <div class="task-modal-backdrop" @click.self="emit('close')">
        <section class="task-modal" role="dialog" aria-modal="true" :aria-label="`Задача ${task.title}`">
            <div ref="detailsElement" class="task-details">
                <div class="task-details-head"><span class="task-mini-label">ЗАДАЧА #{{ task.id }}</span><button class="task-icon-btn" @click="emit('close')" aria-label="Закрыть">×</button></div>
                    <input v-if="editingField === 'title'" v-model="draft" class="task-inline-editor task-inline-title-editor" maxlength="180" aria-label="Название задачи" @blur="commitEdit" @keydown.enter.prevent="commitEdit" @keydown.esc.stop="editingField = null" />
                    <h2 v-else><button class="task-inline-value task-inline-heading" title="Изменить название" @click="beginEdit('title')">{{ task.title }}</button></h2>
                    <div class="task-detail-tags"><span v-if="overdue" class="task-overdue-tag">ЗАДАЧА ПРОСРОЧЕНА</span><select :value="task.status" :disabled="store.saving || overdue" :style="{ color: taskDisplayColor(task), backgroundColor: `${taskDisplayColor(task)}18` }" aria-label="Статус задачи" title="Изменить статус" @change="changeStatus"><option v-for="(label, key) in STATUS_LABELS" :key="key" :value="key">{{ label }}</option></select><span v-if="task.dutyShiftId" class="task-duty-tag">Дежурство · {{ shift?.title ?? '' }}</span></div>
                    <form v-if="overdue" class="task-overdue-form" @submit.prevent="reportOverdue"><strong>Задача просрочена</strong><p>Чтобы продолжить работу, укажите причину и назначьте новый срок. Этот перенос сохранится в личном кабинете.</p><label class="task-field"><span>Причина просрочки</span><textarea v-model="reason" rows="3" maxlength="1000" required placeholder="Что помешало выполнить задачу вовремя?" /></label><div class="task-field"><span>Новый дедлайн</span><DeadlinePicker :value="fromLocalInput(nextDueAt)" :allow-clear="false" @apply="nextDueAt = toLocalInput($event)" /></div><span v-if="overdueError" class="task-inline-error">{{ overdueError }}</span><button class="task-btn primary task-full" :disabled="store.saving || !reason.trim() || !nextDueAt">Сохранить причину и новый срок</button></form>
                    <div class="task-detail-row"><span>Ответственные</span><TaskAssigneesSelect :people="store.users" :selected-ids="taskAssignees(task).map((person) => person.id)" :disabled="store.saving || overdue" compact @change="changeAssignees" /></div>
                    <div class="task-detail-row"><span>Дедлайн</span><DeadlinePicker :value="task.dueAt" :compact="true" :disabled="overdue" @apply="changeDeadline" /></div>
                    <div class="task-detail-row"><span>Приоритет</span><select class="task-inline-select" :value="task.priority" :disabled="store.saving" aria-label="Приоритет" @change="changeChoice('priority', $event)"><option value="low">Низкий</option><option value="normal">Обычный</option><option value="high">Высокий</option></select></div>
                    <div class="task-detail-row"><span>Поставил</span><select class="task-inline-select" :value="(task.requestedBy ?? task.createdBy)?.id ?? ''" :disabled="store.saving || !canEditRequester" aria-label="Поставил задачу" @change="changeChoice('requestedById', $event)"><option value="" disabled>По расписанию дежурства</option><option v-for="person in store.users" :key="person.id" :value="person.id">{{ person.name }}</option></select></div>
                    <div v-if="task.createdBy && task.requestedBy && task.createdBy.id !== task.requestedBy.id" class="task-detail-row task-actual-creator"><span>Создал запись</span><strong>{{ task.createdBy.name }}</strong></div>
                    <div class="task-detail-text"><span>Описание</span><textarea v-if="editingField === 'description'" v-model="draft" class="task-inline-editor task-inline-textarea" rows="4" maxlength="3000" aria-label="Описание" @blur="commitEdit" @keydown.ctrl.enter.prevent="commitEdit" @keydown.esc.stop="editingField = null" /><button v-else class="task-inline-value task-inline-paragraph" title="Изменить описание" @click="beginEdit('description')">{{ task.description || 'Добавить описание' }}</button></div>
                    <div class="task-detail-text"><span>Результат</span><textarea v-if="editingField === 'result'" v-model="draft" class="task-inline-editor task-inline-textarea" rows="3" maxlength="3000" aria-label="Результат" @blur="commitEdit" @keydown.ctrl.enter.prevent="commitEdit" @keydown.esc.stop="editingField = null" /><button v-else class="task-inline-value task-inline-paragraph" title="Изменить результат" @click="beginEdit('result')">{{ task.result || 'Добавить результат' }}</button></div>
                    <div v-if="previousReports.length" class="task-detail-text"><span>История переносов</span><p v-for="item in previousReports" :key="item.id" class="task-overdue-history">{{ formatDate(item.previousDueAt) }} → {{ formatDate(item.newDueAt) }}<br />{{ item.reason }}</p></div>
            </div>
            <div class="task-chat-panel"><div class="task-chat-head"><div><strong>Обсуждение задачи</strong><span>Результат и подтверждение можно отправить сюда</span></div><button class="task-icon-btn task-chat-close" @click="emit('close')" aria-label="Закрыть">×</button></div>
                <div class="task-chat-messages"><div v-if="!messages.length" class="task-empty-chat">Сообщений пока нет. Начните обсуждение задачи.</div><div v-for="message in messages" :key="message.id" class="task-chat-row" :class="{ mine: message.author?.id === store.currentUser?.id }"><div class="task-message"><strong>{{ message.author?.name ?? 'Сотрудник' }}</strong><p>{{ message.body }}</p><time :title="formatDate(message.createdAt)">{{ messageTime(message.createdAt) }}</time></div></div></div>
                <form class="task-chat-compose" @submit.prevent="send"><textarea v-model="draftMessage" rows="2" maxlength="3000" placeholder="Написать сообщение или приложить ссылку на результат…" @keydown.ctrl.enter="send"></textarea><button class="task-btn primary" :disabled="store.saving || !draftMessage.trim()" aria-label="Отправить сообщение">➤</button></form>
            </div>
        </section>
    </div>
</template>
