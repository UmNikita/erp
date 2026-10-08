<script setup lang="ts">
import { onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useTaskStore } from './store';

const store = useTaskStore();
const route = useRoute();
const links = [
    { label: 'Рабочий стол', path: '/tasks' },
    { label: 'Рабочая таблица', path: '/tasks/table' },
    { label: 'Дежурства', path: '/tasks/duties' },
    { label: 'Личный кабинет', path: '/tasks/profile' },
];
onMounted(() => store.load());
</script>

<template>
    <div class="task-layout">
        <aside class="task-side">
            <nav class="task-nav" aria-label="Разделы задач">
                <router-link v-for="link in links" :key="link.path" :to="link.path" :class="{ active: route.path === link.path }">{{ link.label }}</router-link>
                <router-link v-if="store.canAdmin" to="/tasks/admin" :class="{ active: route.path === '/tasks/admin' }">Администрирование</router-link>
            </nav>
        </aside>
        <main class="task-main">
            <div v-if="store.error" class="task-error" role="alert">{{ store.error }} <button @click="store.clearError()" aria-label="Закрыть">×</button></div>
            <div v-if="store.loading && !store.currentUser" class="task-loading">Загружаем задачи…</div>
            <router-view v-else />
        </main>
    </div>
</template>
