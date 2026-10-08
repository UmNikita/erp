<script setup lang="ts">
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { formatDate, toLocalInput } from '../types';

const props = withDefaults(defineProps<{ value: string | null; allowClear?: boolean; compact?: boolean; disabled?: boolean }>(), { allowClear: true, compact: false, disabled: false });
const emit = defineEmits<{ apply: [value: string | null] }>();
const trigger = ref<HTMLElement | null>(null);
const panel = ref<HTMLElement | null>(null);
const open = ref(false);
const selectedDate = ref('');
const selectedTime = ref('18:00');
const cursor = ref(new Date(new Date().getFullYear(), new Date().getMonth(), 1, 12));
const position = ref({ left: 0, top: 0 });
const weekdays = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
const monthFormatter = new Intl.DateTimeFormat('ru-RU', { month: 'long', year: 'numeric' });
const monthLabel = computed(() => {
    const label = monthFormatter.format(cursor.value);
    return label.charAt(0).toUpperCase() + label.slice(1);
});
const days = computed(() => {
    const first = new Date(cursor.value.getFullYear(), cursor.value.getMonth(), 1, 12);
    const offset = (first.getDay() + 6) % 7;
    return Array.from({ length: 42 }, (_, index) => {
        const date = new Date(first.getFullYear(), first.getMonth(), 1 - offset + index, 12);
        return { iso: localDate(date), day: date.getDate(), outside: date.getMonth() !== first.getMonth() };
    });
});
function localDate(date: Date): string {
    return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
}
function chooseOffset(daysFromToday: number): void {
    const date = new Date();
    date.setDate(date.getDate() + daysFromToday);
    selectedDate.value = localDate(date);
    cursor.value = new Date(date.getFullYear(), date.getMonth(), 1, 12);
}
function moveMonth(offset: number): void {
    cursor.value = new Date(cursor.value.getFullYear(), cursor.value.getMonth() + offset, 1, 12);
}
function sync(): void {
    const local = toLocalInput(props.value);
    selectedDate.value = local.slice(0, 10);
    selectedTime.value = local.slice(11, 16) || '18:00';
    const date = selectedDate.value ? new Date(`${selectedDate.value}T12:00:00`) : new Date();
    cursor.value = new Date(date.getFullYear(), date.getMonth(), 1, 12);
}
watch(() => props.value, () => { if (!open.value) sync(); });
function closeOutside(event: MouseEvent): void {
    const target = event.target as Node;
    if (!trigger.value?.contains(target) && !panel.value?.contains(target)) open.value = false;
}
function onEscape(event: KeyboardEvent): void {
    if (!open.value || event.key !== 'Escape') return;
    open.value = false;
    event.stopPropagation();
}
onMounted(() => { document.addEventListener('mousedown', closeOutside); document.addEventListener('keydown', onEscape, true); });
onBeforeUnmount(() => { document.removeEventListener('mousedown', closeOutside); document.removeEventListener('keydown', onEscape, true); });
async function toggle(): Promise<void> {
    if (open.value) { open.value = false; return; }
    sync();
    const rect = trigger.value?.getBoundingClientRect();
    if (!rect) return;
    const width = 324;
    const height = 390;
    position.value = {
        left: Math.max(8, Math.min(rect.right - width, window.innerWidth - width - 8)),
        top: rect.bottom + height + 8 <= window.innerHeight ? rect.bottom + 6 : Math.max(8, rect.top - height - 6),
    };
    open.value = true;
    await nextTick();
    const actual = panel.value?.getBoundingClientRect();
    if (actual && actual.bottom > window.innerHeight - 8) position.value.top = Math.max(8, window.innerHeight - actual.height - 8);
}
function apply(): void {
    if (!selectedDate.value) return;
    const date = new Date(`${selectedDate.value}T${selectedTime.value || '18:00'}:00`);
    if (Number.isNaN(date.getTime())) return;
    emit('apply', date.toISOString());
    open.value = false;
}
function clear(): void { emit('apply', null); open.value = false; }
</script>

<template>
    <button ref="trigger" type="button" class="task-deadline-trigger" :class="{ compact }" :disabled="disabled" :aria-expanded="open" aria-label="Выбрать дедлайн" @click="toggle">{{ value ? formatDate(value) : 'Без дедлайна' }}<span class="task-deadline-caret">⌄</span></button>
    <Teleport to="#tasks-app">
        <div v-if="open" ref="panel" class="task-deadline-popover" :style="{ left: `${position.left}px`, top: `${position.top}px` }">
            <div class="task-deadline-top"><strong>Дедлайн</strong><button type="button" aria-label="Закрыть календарь" @click="open = false">×</button></div>
            <div class="task-deadline-presets"><button type="button" @click="chooseOffset(0)">Сегодня</button><button type="button" @click="chooseOffset(1)">Завтра</button><button type="button" @click="chooseOffset(7)">Через неделю</button></div>
            <div class="task-deadline-month"><button type="button" aria-label="Предыдущий месяц" @click="moveMonth(-1)">‹</button><strong>{{ monthLabel }}</strong><button type="button" aria-label="Следующий месяц" @click="moveMonth(1)">›</button></div>
            <div class="task-deadline-week"><span v-for="day in weekdays" :key="day">{{ day }}</span></div>
            <div class="task-deadline-days"><button v-for="day in days" :key="day.iso" type="button" :class="{ outside: day.outside, selected: day.iso === selectedDate, today: day.iso === localDate(new Date()) }" @click="selectedDate = day.iso; if (day.outside) cursor = new Date(Number(day.iso.slice(0, 4)), Number(day.iso.slice(5, 7)) - 1, 1, 12)">{{ day.day }}</button></div>
            <div class="task-deadline-time"><span>Время</span><input v-model="selectedTime" type="time" aria-label="Время дедлайна" /></div>
            <div class="task-deadline-actions"><button v-if="allowClear" type="button" class="task-deadline-clear" @click="clear">Без дедлайна</button><button type="button" class="task-deadline-apply" :disabled="!selectedDate" @click="apply">Применить</button></div>
        </div>
    </Teleport>
</template>
