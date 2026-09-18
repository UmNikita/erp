<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { formatPeriodLabel, lastDaysRange, monthRange, parseLocalIso, toLocalIso, type DateRange } from '../utils/dateRange';

interface CalendarDay { iso: string; number: number; outside: boolean }
interface Preset { label: string; range: () => DateRange }

const props = defineProps<{ from: string; to: string }>();
const emit = defineEmits<{ apply: [from: string, to: string] }>();
const root = ref<HTMLElement | null>(null);
const open = ref(false);
const draftFrom = ref(props.from);
const draftTo = ref(props.to);
const cursor = ref(monthStart(parseLocalIso(props.from)));
const choosingEnd = ref(false);
const validationError = ref('');

const label = computed(() => formatPeriodLabel(props.from, props.to));
const leftMonth = computed(() => monthStart(cursor.value));
const rightMonth = computed(() => addMonths(leftMonth.value, 1));
const leftDays = computed(() => calendarDays(leftMonth.value));
const rightDays = computed(() => calendarDays(rightMonth.value));
const weekdays = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
const monthFormatter = new Intl.DateTimeFormat('ru-RU', { month: 'long', year: 'numeric' });

const presets: Preset[] = [
    { label: 'Сегодня', range: todayRange },
    { label: 'Вчера', range: yesterdayRange },
    { label: 'Эта неделя', range: () => weekRange(0) },
    { label: 'Последние 7 дней', range: () => lastDaysRange(7) },
    { label: 'Прошлая неделя', range: () => weekRange(-1) },
    { label: 'Этот месяц', range: () => monthRange() },
    { label: 'Последние 30 дней', range: () => lastDaysRange(30) },
    { label: 'Прошлый месяц', range: () => monthRange(-1) },
    { label: 'Этот квартал', range: () => quarterRange(0) },
    { label: 'Прошлый квартал', range: () => quarterRange(-1) },
    { label: 'Этот год', range: () => yearRange(0) },
    { label: 'Прошлый год', range: () => yearRange(-1) },
];

watch(() => [props.from, props.to], ([from, to]) => {
    draftFrom.value = from;
    draftTo.value = to;
});

function monthStart(date: Date): Date { return new Date(date.getFullYear(), date.getMonth(), 1, 12); }
function addMonths(date: Date, amount: number): Date { return new Date(date.getFullYear(), date.getMonth() + amount, 1, 12); }

function todayRange(): DateRange {
    const today = toLocalIso(new Date());
    return { from: today, to: today };
}

function yesterdayRange(): DateRange {
    const date = new Date();
    date.setDate(date.getDate() - 1);
    const value = toLocalIso(date);
    return { from: value, to: value };
}

function weekRange(offset: number): DateRange {
    const today = new Date();
    const mondayOffset = (today.getDay() + 6) % 7;
    const monday = new Date(today.getFullYear(), today.getMonth(), today.getDate() - mondayOffset + offset * 7, 12);
    const sunday = new Date(monday.getFullYear(), monday.getMonth(), monday.getDate() + 6, 12);
    return { from: toLocalIso(monday), to: toLocalIso(sunday) };
}

function quarterRange(offset: number): DateRange {
    const today = new Date();
    const quarter = Math.floor(today.getMonth() / 3) + offset;
    const start = new Date(today.getFullYear(), quarter * 3, 1, 12);
    const end = new Date(start.getFullYear(), start.getMonth() + 3, 0, 12);
    return { from: toLocalIso(start), to: toLocalIso(end) };
}

function yearRange(offset: number): DateRange {
    const year = new Date().getFullYear() + offset;
    return { from: `${year}-01-01`, to: `${year}-12-31` };
}

function calendarDays(month: Date): CalendarDay[] {
    const mondayIndex = (month.getDay() + 6) % 7;
    const first = new Date(month.getFullYear(), month.getMonth(), 1 - mondayIndex, 12);
    return Array.from({ length: 42 }, (_, index) => {
        const date = new Date(first.getFullYear(), first.getMonth(), first.getDate() + index, 12);
        return { iso: toLocalIso(date), number: date.getDate(), outside: date.getMonth() !== month.getMonth() };
    });
}

function monthLabel(date: Date): string {
    const value = monthFormatter.format(date);
    return value.charAt(0).toUpperCase() + value.slice(1);
}

function sameRange(range: DateRange): boolean { return draftFrom.value === range.from && draftTo.value === range.to; }

function selectPreset(range: DateRange): void {
    draftFrom.value = range.from;
    draftTo.value = range.to;
    cursor.value = monthStart(parseLocalIso(range.from));
    choosingEnd.value = false;
    validationError.value = '';
}

function selectDay(iso: string): void {
    validationError.value = '';
    if (!choosingEnd.value) {
        draftFrom.value = iso;
        draftTo.value = iso;
        choosingEnd.value = true;
        return;
    }
    if (iso < draftFrom.value) {
        draftTo.value = draftFrom.value;
        draftFrom.value = iso;
    } else draftTo.value = iso;
    choosingEnd.value = false;
}

function dayClasses(day: CalendarDay): Record<string, boolean> {
    return {
        outside: day.outside,
        today: day.iso === toLocalIso(new Date()),
        'range-start': !day.outside && day.iso === draftFrom.value,
        'range-end': !day.outside && day.iso === draftTo.value,
        'in-range': !day.outside && day.iso > draftFrom.value && day.iso < draftTo.value,
    };
}

function syncFromInputs(): void {
    if (draftFrom.value) cursor.value = monthStart(parseLocalIso(draftFrom.value));
    validationError.value = '';
}

function applyCustom(): void {
    if (!draftFrom.value || !draftTo.value || draftFrom.value > draftTo.value) {
        validationError.value = 'Проверьте начало и конец периода';
        return;
    }
    emit('apply', draftFrom.value, draftTo.value);
    open.value = false;
}

function cancel(): void {
    draftFrom.value = props.from;
    draftTo.value = props.to;
    open.value = false;
}

function toggle(): void {
    draftFrom.value = props.from;
    draftTo.value = props.to;
    cursor.value = monthStart(parseLocalIso(props.from));
    choosingEnd.value = false;
    validationError.value = '';
    open.value = !open.value;
}

function closeOutside(event: MouseEvent): void {
    if (root.value && !root.value.contains(event.target as Node)) cancel();
}

onMounted(() => document.addEventListener('mousedown', closeOutside));
onBeforeUnmount(() => document.removeEventListener('mousedown', closeOutside));
</script>

<template>
    <div ref="root" class="period-picker">
        <button class="period-trigger" type="button" :aria-expanded="open" @click="toggle">
            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>
            <span>{{ label }}</span><i>⌄</i>
        </button>

        <div v-if="open" class="period-popover period-popover-calendar" @keydown.esc="cancel">
            <aside class="period-preset-list">
                <button v-for="preset in presets" :key="preset.label" type="button" :class="{ active: sameRange(preset.range()) }" @click="selectPreset(preset.range())">{{ preset.label }}</button>
            </aside>

            <div class="period-calendar-main">
                <div class="period-date-row">
                    <label><span>С</span><input v-model="draftFrom" type="date" @change="syncFromInputs"></label>
                    <span class="period-date-separator">—</span>
                    <label><span>По</span><input v-model="draftTo" type="date" @change="syncFromInputs"></label>
                    <button class="period-close" type="button" aria-label="Закрыть" @click="cancel">×</button>
                </div>

                <div class="period-calendar-navigation">
                    <button type="button" aria-label="Предыдущий месяц" @click="cursor = addMonths(cursor, -1)">‹</button>
                    <span>{{ monthLabel(leftMonth) }} — {{ monthLabel(rightMonth) }}</span>
                    <button type="button" aria-label="Следующий месяц" @click="cursor = addMonths(cursor, 1)">›</button>
                </div>

                <div class="period-calendars">
                    <section v-for="month in [{ date: leftMonth, days: leftDays }, { date: rightMonth, days: rightDays }]" :key="month.date.toISOString()" class="period-month">
                        <strong>{{ monthLabel(month.date) }}</strong>
                        <div class="period-weekdays"><span v-for="day in weekdays" :key="day">{{ day }}</span></div>
                        <div class="period-days">
                            <button v-for="day in month.days" :key="`${month.date.getMonth()}-${day.iso}`" type="button" :class="dayClasses(day)" @click="selectDay(day.iso)">{{ day.number }}</button>
                        </div>
                    </section>
                </div>

                <div class="period-calendar-footer">
                    <span v-if="validationError" class="period-error">{{ validationError }}</span>
                    <button class="period-cancel" type="button" @click="cancel">Отменить</button>
                    <button class="period-apply" type="button" @click="applyCustom">Применить</button>
                </div>
            </div>
        </div>
    </div>
</template>
