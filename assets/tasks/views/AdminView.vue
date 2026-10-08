<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useTaskStore } from '../store';
import AdminStatistics from '../components/AdminStatistics.vue';
import AdminAllTasks from '../components/AdminAllTasks.vue';
import AdminAudit from '../components/AdminAudit.vue';
import EmployeeSearchSelect from '../components/EmployeeSearchSelect.vue';

const store = useTaskStore();
const section = ref<'statistics' | 'all' | 'comments'>('statistics');
const recipientId = ref<number | null>(null);
const body = ref('');
const confirmingId = ref<number | null>(null);
watch(() => store.users, (users) => { if (!recipientId.value && users.length) recipientId.value = users[0].id; }, { immediate: true });
watch(recipientId, () => { confirmingId.value = null; });
const comments = computed(() => store.feedback.filter((item) => item.recipient.id === recipientId.value));
const date = (value: string) => new Intl.DateTimeFormat('ru-RU', { day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit' }).format(new Date(value));
async function submit() {
    if (!recipientId.value || !body.value.trim()) return;
    if (await store.addFeedback(recipientId.value, body.value.trim())) body.value = '';
}
async function remove(id: number) {
    if (confirmingId.value !== id || store.saving) return;
    if (await store.deleteFeedback(id)) confirmingId.value = null;
}
</script>

<template>
    <div class="task-page task-admin-page">
        <div v-if="!store.canAdmin" class="task-empty">Раздел доступен только руководителю с правом администрирования задач.</div>
        <template v-else>
            <div class="task-admin-header"><strong>Администрирование задач</strong><div class="task-admin-nav"><button type="button" :class="{ active: section === 'statistics' }" @click="section = 'statistics'">Статистика</button><button type="button" :class="{ active: section === 'all' }" @click="section = 'all'">Все задачи</button><button type="button" :class="{ active: section === 'comments' }" @click="section = 'comments'">Комментарии руководителя</button></div></div>
            <AdminAudit />
            <AdminStatistics v-if="section === 'statistics'" />
            <AdminAllTasks v-else-if="section === 'all'" />
            <div v-else class="task-admin-scroll"><section class="task-insight-card task-admin-feedback">
                <div class="task-insight-head"><div><strong>Комментарии сотруднику</strong><span>Видны выбранному сотруднику на рабочем столе и руководителям с правом администрирования</span></div></div>
                <div class="task-field"><span>Сотрудник</span><EmployeeSearchSelect :people="store.users" :selected-id="recipientId" @select="recipientId = $event" /></div>
                <label class="task-field"><span>Новый комментарий</span><textarea v-model="body" rows="4" maxlength="2000" placeholder="Напишите отзыв, рекомендацию или замечание по работе…" /></label>
                <button class="task-btn primary" :disabled="store.saving || !recipientId || !body.trim()" @click="submit">Опубликовать для сотрудника</button>
                <div class="task-admin-history"><strong>История комментариев</strong><div v-if="!comments.length" class="task-empty">Комментариев пока нет</div><article v-for="item in comments" :key="item.id" class="task-feedback-entry"><div><strong>{{ item.author?.name ?? 'Руководитель' }}</strong><time>{{ date(item.createdAt) }}</time></div><p>{{ item.body }}</p><div class="task-feedback-actions"><template v-if="confirmingId === item.id"><span>Удалить комментарий без возможности восстановления?</span><button type="button" class="task-feedback-delete confirm" :disabled="store.saving" @click="remove(item.id)">Да, удалить</button><button type="button" class="task-feedback-cancel" :disabled="store.saving" @click="confirmingId = null">Отмена</button></template><button v-else type="button" class="task-feedback-delete" :disabled="store.saving" @click="confirmingId = item.id">Удалить</button></div></article></div>
            </section></div>
        </template>
    </div>
</template>
