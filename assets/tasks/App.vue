<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import ManagerComments from './components/ManagerComments.vue';
import ModuleSidebar from '../shared/ModuleSidebar.vue';
import { useTaskStore } from './store';

const store = useTaskStore();
const route = useRoute();
const adminPage = computed(() => route.path === '/tasks/admin' || route.path.startsWith('/tasks/admin/') || route.path.startsWith('/tasks/duties/'));
const commentsCollapsed = ref(false);
const links = computed(() => [
    { name: 'Рабочий стол', path: '/tasks' },
    { name: 'Рабочая таблица', path: '/tasks/table' },
    { name: 'Дежурства', path: '/tasks/duties' },
    { name: 'Личная история', path: '/tasks/profile' },
    { name: 'Рейтинг команды', path: '/tasks/ranking' },
    ...(store.canAdmin ? [{ name: 'Администрирование', path: '/tasks/admin' }] : []),
]);
let feedbackTimer: ReturnType<typeof setInterval>;
const refreshFeedback = () => { if (document.visibilityState === 'visible') void store.refreshFeedback(); };
onMounted(() => {
    void store.load();
    feedbackTimer = setInterval(refreshFeedback, 30000);
    document.addEventListener('visibilitychange', refreshFeedback);
    window.addEventListener('focus', refreshFeedback);
});
onUnmounted(() => {
    clearInterval(feedbackTimer);
    document.removeEventListener('visibilitychange', refreshFeedback);
    window.removeEventListener('focus', refreshFeedback);
});
</script>

<template>
    <div class="task-layout" :class="{ 'with-manager-menu': route.path === '/tasks', 'manager-collapsed': commentsCollapsed }">
        <ModuleSidebar :links="links" label="Разделы задач" />
        <main class="task-main">
            <div v-if="store.error" class="task-error" role="alert">{{ store.error }} <button @click="store.clearError()" aria-label="Закрыть">×</button></div>
            <div v-if="!store.currentUser" class="task-loading">
                <template v-if="store.loading">Загружаем задачи…</template>
                <div v-else><p>Не удалось загрузить задачник.</p><button class="task-btn primary" @click="store.load()">Повторить загрузку</button></div>
            </div>
            <div v-else-if="adminPage && !store.canAdmin" class="task-page task-empty" role="alert">Этот раздел доступен только администратору задач.<router-link to="/tasks" class="task-btn outline">Вернуться к задачам</router-link></div>
            <router-view v-else />
        </main>
        <ManagerComments v-if="route.path === '/tasks' && store.currentUser" :collapsed="commentsCollapsed" @toggle="commentsCollapsed = !commentsCollapsed" />
    </div>
</template>
