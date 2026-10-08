<script setup lang="ts">
import { computed } from 'vue';
import { useTaskStore } from '../store';

const store = useTaskStore();
const daily = computed(() => store.dashboard?.daily ?? []);
const personal = computed(() => store.dashboard?.personal);
const ranking = computed(() => store.dashboard?.ranking ?? []);
const maxTick = computed(() => Math.max(1, ...daily.value.flatMap((day) => [day.done, day.failed])));
const chartHeight = computed(() => Math.min(840, Math.max(190, maxTick.value * 20 + 35)));
const baseline = computed(() => chartHeight.value - 18);
const plotHeight = computed(() => baseline.value - 24);
const y = (count: number) => baseline.value - count / maxTick.value * plotHeight.value;
const x = (index: number) => 50 + index * (500 / 6);
const points = (kind: 'done' | 'failed') => daily.value.map((day, index) => `${x(index)},${y(day[kind])}`).join(' ');
const area = (kind: 'done' | 'failed') => `${x(0)},${baseline.value} ${points(kind)} ${x(6)},${baseline.value}`;
const dayLabel = (value: string) => new Intl.DateTimeFormat('ru-RU', { day: 'numeric', month: 'short', timeZone: 'Europe/Moscow' }).format(new Date(`${value}T12:00:00+03:00`));
const percentage = (value: number | null | undefined) => value == null ? '—' : `${value}%`;
</script>

<template>
    <section class="task-insights" aria-label="Статистика и рейтинг">
        <div class="task-insight-card task-insight-stats">
            <div class="task-insight-head"><div><strong>Мои результаты</strong><span>Последние 7 дней · по дате закрытия задачи</span></div><div class="task-chart-legend"><span class="done">Выполнено</span><span class="failed">Не выполнено</span></div></div>
            <div class="task-insight-metrics">
                <div><strong>{{ personal?.done ?? 0 }}</strong><span>выполнено</span></div>
                <div><strong>{{ personal?.failed ?? 0 }}</strong><span>не выполнено</span></div>
                <div><strong>{{ percentage(personal?.completionRate) }}</strong><span>доля выполненных</span></div>
                <div title="Доля закрытых задач с дедлайном, выполненных до него без переноса"><strong>{{ percentage(personal?.onTimeRate) }}</strong><span>в срок</span></div>
                <div title="Доля закрытых задач с дедлайном, для которых запрашивали новый срок"><strong>{{ percentage(personal?.transferRate) }}</strong><span>с переносом</span></div>
            </div>
            <div v-if="daily.length" class="task-chart-wrap">
                <svg class="task-trend-chart" :viewBox="`0 0 600 ${chartHeight}`" preserveAspectRatio="none" role="img" aria-label="Динамика выполненных и невыполненных задач. Каждая горизонтальная линия равна одной задаче.">
                    <defs><linearGradient id="task-done-fill" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#29c995" stop-opacity=".24" /><stop offset="100%" stop-color="#29c995" stop-opacity="0" /></linearGradient><linearGradient id="task-failed-fill" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#ec6d7d" stop-opacity=".16" /><stop offset="100%" stop-color="#ec6d7d" stop-opacity="0" /></linearGradient></defs>
                    <polygon :points="area('done')" fill="url(#task-done-fill)" />
                    <polygon :points="area('failed')" fill="url(#task-failed-fill)" />
                    <g v-for="level in maxTick + 1" :key="level" class="task-chart-level"><line x1="36" :y1="y(level - 1)" x2="565" :y2="y(level - 1)" /><text x="21" :y="y(level - 1) + 3" text-anchor="end">{{ level - 1 }}</text></g>
                    <polyline :points="points('done')" class="task-chart-line done" />
                    <polyline :points="points('failed')" class="task-chart-line failed" />
                </svg>
                <div class="task-chart-days"><span v-for="day in daily" :key="day.date">{{ dayLabel(day.date) }}</span></div>
            </div>
        </div>
        <div class="task-insight-card task-ranking-card">
            <div class="task-insight-head"><div><strong>Рейтинг команды</strong><span>Последние 30 дней · только общие показатели</span></div><span class="task-ranking-trophy">★</span></div>
            <div v-if="!ranking.length" class="task-empty">Пока нет результатов</div>
            <div v-for="person in ranking" :key="person.person.id" class="task-ranking-row" :class="{ mine: person.person.id === store.currentUser?.id }"><span class="task-ranking-position">{{ person.rank }}</span><span class="task-ranking-name">{{ person.person.name }}<small v-if="person.person.id === store.currentUser?.id">Вы</small></span><span class="task-ranking-count">{{ person.done }} задач</span><strong>{{ person.score }}</strong></div>
            <p class="task-stat-note">Баллы: +10 за выполненную, +2 за выполнение в срок, −4 за невыполненную, −2 за перенос. Чужие задачи и комментарии здесь не показываются.</p>
        </div>
    </section>
</template>
