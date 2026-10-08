<script setup lang="ts">
import { reactive, watch } from 'vue';
import { useTaskStore } from '../store';
import DeadlinePicker from './DeadlinePicker.vue';
import TaskAssigneesSelect from './TaskAssigneesSelect.vue';
import { fromLocalInput, taskAssignees, toLocalInput, type TaskItem, type TaskPriority, type TaskStatus } from '../types';

const props = defineProps<{ task?: TaskItem | null }>();
const emit = defineEmits<{ close: [] }>();
const store = useTaskStore();
const form = reactive({ title: '', description: '', result: '', status: 'open' as TaskStatus, priority: 'normal' as TaskPriority, dueAt: '', assigneeIds: [] as number[], requestedById: 0 });
watch(() => props.task, (task) => {
    form.title = task?.title ?? '';
    form.description = task?.description ?? '';
    form.result = task?.result ?? '';
    form.status = task?.status ?? 'open';
    form.priority = task?.priority ?? 'normal';
    form.dueAt = toLocalInput(task?.dueAt ?? null);
    form.assigneeIds = task ? taskAssignees(task).map((person) => person.id) : (store.currentUser ? [store.currentUser.id] : []);
    form.requestedById = task?.requestedBy?.id ?? task?.createdBy?.id ?? store.currentUser?.id ?? 0;
}, { immediate: true });

async function save() {
    if (!form.title.trim() || !form.assigneeIds.length) return;
    const payload = { title: form.title.trim(), description: form.description, result: form.result, status: form.status, priority: form.priority, dueAt: fromLocalInput(form.dueAt), assigneeIds: form.assigneeIds, requestedById: form.requestedById };
    const ok = props.task ? await store.updateTask(props.task.id, payload) : await store.createTask(payload);
    if (ok) emit('close');
}
</script>

<template>
    <form class="task-editor" @submit.prevent="save">
        <div class="task-editor-head"><strong>{{ task ? 'Редактировать задачу' : 'Новая задача' }}</strong><button type="button" class="task-icon-btn" @click="emit('close')" aria-label="Закрыть">×</button></div>
        <label class="task-field"><span>Название</span><input v-model="form.title" maxlength="180" required placeholder="Что нужно сделать?" /></label>
        <div class="task-form-grid">
            <div class="task-field"><span>Ответственные</span><TaskAssigneesSelect :people="store.users" :selected-ids="form.assigneeIds" @change="form.assigneeIds = $event" /></div>
            <div class="task-field"><span>Дедлайн</span><DeadlinePicker :value="fromLocalInput(form.dueAt)" @apply="form.dueAt = toLocalInput($event)" /></div>
            <label class="task-field"><span>Поставил</span><select v-model.number="form.requestedById" :disabled="!store.canAdmin && (!task || task.createdBy?.id !== store.currentUser?.id)"><option v-for="person in store.users" :key="person.id" :value="person.id">{{ person.name }}</option></select></label>
            <label class="task-field"><span>Статус</span><select v-model="form.status"><option value="open">К работе</option><option value="in_progress">В работе</option><option value="review">На проверке</option><option value="done">Выполнена</option><option value="not_done">Не выполнена</option></select></label>
            <label class="task-field"><span>Приоритет</span><select v-model="form.priority"><option value="low">Низкий</option><option value="normal">Обычный</option><option value="high">Высокий</option></select></label>
        </div>
        <label class="task-field"><span>Описание</span><textarea v-model="form.description" maxlength="3000" rows="3" placeholder="Контекст и ожидаемый результат" /></label>
        <label v-if="task" class="task-field"><span>Результат</span><textarea v-model="form.result" maxlength="3000" rows="2" placeholder="Что получилось — можно добавить ссылку на результат" /></label>
        <div class="task-actions"><button type="button" class="task-btn ghost" @click="emit('close')">Отмена</button><button class="task-btn primary" :disabled="store.saving || !form.title.trim() || !form.assigneeIds.length">{{ store.saving ? 'Сохраняем…' : task ? 'Сохранить' : 'Создать задачу' }}</button></div>
    </form>
</template>
