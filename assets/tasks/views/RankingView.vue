<script setup lang="ts">
import { useTaskStore } from '../store';
const store = useTaskStore();
</script>
<template>
    <div class="task-page task-ranking-page">
        <div class="task-toolbar"><h1 class="task-page-title">Рейтинг команды</h1><router-link class="task-btn outline" to="/tasks">К рабочему столу</router-link></div>
        <div class="task-ranking-page-scroll">
            <section class="task-insight-card">
                <div class="task-insight-head"><div><strong>Результаты за последние 30 дней</strong><span>Общие показатели сотрудников — без чужих задач и комментариев</span></div></div>
                <div class="task-ranking-table-wrap"><table class="task-ranking-table">
                    <thead><tr><th>Место</th><th>Сотрудник</th><th>Выполнено</th><th>В срок</th><th>Не выполнено</th><th>Переносы</th><th>Баллы</th></tr></thead>
                    <tbody><tr v-for="row in store.dashboard?.ranking ?? []" :key="row.person.id" :class="{ mine: row.person.id === store.currentUser?.id }"><td>{{ row.rank }}</td><td>{{ row.person.name }}<small v-if="row.person.id === store.currentUser?.id"> · Вы</small></td><td>{{ row.done }}</td><td>{{ row.onTime }}</td><td>{{ row.failed }}</td><td>{{ row.moved }}</td><td><strong>{{ row.score }}</strong></td></tr></tbody>
                </table></div>
                <p v-if="!store.dashboard?.ranking.length" class="task-empty">Результаты появятся после закрытия задач.</p>
            </section>
            <section class="task-insight-card task-ranking-rules"><h2>Как считаются баллы</h2><p>+10 за выполненную задачу, дополнительно +2 за выполнение в срок, −4 за невыполненную задачу и −2 за перенос срока.</p><p>Рейтинг обновляется по результатам работы за последние 30 дней.</p></section>
        </div>
    </div>
</template>
