<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { formatPeriodLabel, lastDaysRange, lastMonthsRange, monthRange, type DateRange } from '../utils/dateRange';

const props = defineProps<{ from: string; to: string }>();
const emit = defineEmits<{ apply: [from: string, to: string] }>();
const root = ref<HTMLElement | null>(null);
const open = ref(false);
const draftFrom = ref(props.from);
const draftTo = ref(props.to);
const validationError = ref('');

const label = computed(() => formatPeriodLabel(props.from, props.to));
const current = computed(() => monthRange());
const previous = computed(() => monthRange(-1));
const last30 = computed(() => lastDaysRange(30));
const last90 = computed(() => lastMonthsRange(3));

watch(() => [props.from, props.to], ([from, to]) => {
    draftFrom.value = from;
    draftTo.value = to;
});

function sameRange(range: DateRange): boolean {
    return props.from === range.from && props.to === range.to;
}

function applyRange(range: DateRange): void {
    validationError.value = '';
    emit('apply', range.from, range.to);
    open.value = false;
}

function applyCustom(): void {
    if (!draftFrom.value || !draftTo.value || draftFrom.value > draftTo.value) {
        validationError.value = 'Проверьте начало и конец периода';
        return;
    }
    applyRange({ from: draftFrom.value, to: draftTo.value });
}

function toggle(): void {
    draftFrom.value = props.from;
    draftTo.value = props.to;
    validationError.value = '';
    open.value = !open.value;
}

function closeOutside(event: MouseEvent): void {
    if (root.value && !root.value.contains(event.target as Node)) open.value = false;
}

onMounted(() => document.addEventListener('mousedown', closeOutside));
onBeforeUnmount(() => document.removeEventListener('mousedown', closeOutside));
</script>

<template>
    <div ref="root" class="period-picker">
        <button class="period-trigger" type="button" :aria-expanded="open" @click="toggle">
            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>
            <span>{{ label }}</span>
            <i>⌄</i>
        </button>

        <div v-if="open" class="period-popover" @keydown.esc="open = false">
            <div class="period-popover-head">
                <strong>Выберите период</strong>
                <button type="button" aria-label="Закрыть" @click="open = false">×</button>
            </div>
            <div class="period-presets">
                <button type="button" :class="{ active: sameRange(current) }" @click="applyRange(current)">Этот месяц</button>
                <button type="button" :class="{ active: sameRange(previous) }" @click="applyRange(previous)">Прошлый месяц</button>
                <button type="button" :class="{ active: sameRange(last30) }" @click="applyRange(last30)">Последние 30 дней</button>
                <button type="button" :class="{ active: sameRange(last90) }" @click="applyRange(last90)">Последние 3 месяца</button>
            </div>
            <div class="period-custom-title">Произвольные даты</div>
            <div class="period-fields">
                <label><span>С даты</span><input v-model="draftFrom" type="date"></label>
                <label><span>По дату</span><input v-model="draftTo" type="date"></label>
            </div>
            <div v-if="validationError" class="period-error">{{ validationError }}</div>
            <button class="period-apply" type="button" @click="applyCustom">Применить период</button>
        </div>
    </div>
</template>
