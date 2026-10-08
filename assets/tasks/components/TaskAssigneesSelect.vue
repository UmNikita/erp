<script setup lang="ts">
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import type { Person } from '../types';

const props = withDefaults(defineProps<{ people: Person[]; selectedIds: number[]; disabled?: boolean; compact?: boolean }>(), { disabled: false, compact: false });
const emit = defineEmits<{ change: [ids: number[]] }>();
const root = ref<HTMLElement | null>(null);
const input = ref<HTMLInputElement | null>(null);
const open = ref(false);
const query = ref('');
const selected = computed(() => props.people.filter((person) => props.selectedIds.includes(person.id)));
const matches = computed(() => props.people.filter((person) => person.name.toLocaleLowerCase('ru-RU').includes(query.value.trim().toLocaleLowerCase('ru-RU'))));
const label = computed(() => selected.value.map((person) => person.name).join(', ') || 'Выберите ответственных');
async function toggle() {
    if (props.disabled) return;
    open.value = !open.value;
    if (open.value) { query.value = ''; await nextTick(); input.value?.focus(); }
}
function change(id: number, event: Event) {
    const checkbox = event.target as HTMLInputElement;
    const ids = checkbox.checked ? [...props.selectedIds, id] : props.selectedIds.filter((value) => value !== id);
    if (!ids.length) { checkbox.checked = true; return; }
    emit('change', ids);
}
function closeOutside(event: MouseEvent) { if (!root.value?.contains(event.target as Node)) open.value = false; }
onMounted(() => document.addEventListener('mousedown', closeOutside));
onBeforeUnmount(() => document.removeEventListener('mousedown', closeOutside));
</script>

<template>
    <div ref="root" class="task-assignees-select" :class="{ compact }">
        <button type="button" class="task-assignees-trigger" :disabled="disabled" :aria-expanded="open" :title="label" @click="toggle"><span>{{ label }}</span><span aria-hidden="true">⌄</span></button>
        <div v-if="open" class="task-assignees-popover" @keydown.esc.stop="open = false"><input ref="input" v-model="query" type="search" aria-label="Поиск ответственных" placeholder="Поиск сотрудника…" /><div class="task-assignees-options"><label v-for="person in matches" :key="person.id"><input type="checkbox" :checked="selectedIds.includes(person.id)" :disabled="disabled" @change="change(person.id, $event)" /><span>{{ person.name }}</span></label><div v-if="!matches.length" class="task-employee-no-match">Сотрудник не найден</div></div><small>Выберите одного или нескольких</small></div>
    </div>
</template>
