<script setup lang="ts">
import { computed } from 'vue';
import { useTaskStore } from '../store';
const store = useTaskStore();
const ranking = computed(() => (store.dashboard?.ranking ?? []).slice(0, 3));
</script>
<template>
    <section class="task-ranking-card" aria-label="Рейтинг команды">
        <div class="task-insight-head"><div><strong>Рейтинг команды</strong><span>Последние 30 дней · общие показатели</span></div><span class="task-ranking-trophy" aria-hidden="true">★</span></div>
        <div v-if="!ranking.length" class="task-empty">Пока нет результатов</div>
        <div v-for="person in ranking" :key="person.person.id" class="task-ranking-row" :class="{ mine: person.person.id === store.currentUser?.id }"><span class="task-ranking-position">{{ person.rank }}</span><span class="task-ranking-name" :title="person.person.name">{{ person.person.name }}<small v-if="person.person.id === store.currentUser?.id">Вы</small><span class="task-ranking-count">{{ person.done }} задач</span></span><strong title="Баллы">{{ person.score }}</strong></div>
        <router-link to="/tasks/ranking" class="task-ranking-link">Подробнее о рейтинге →</router-link>
    </section>
</template>
