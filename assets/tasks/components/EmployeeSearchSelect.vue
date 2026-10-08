<script setup lang="ts">
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import type { Person } from '../types';

const props = defineProps<{ people: Person[]; selectedId: number | null }>();
const emit = defineEmits<{ select: [id: number] }>();
const root = ref<HTMLElement | null>(null);
const input = ref<HTMLInputElement | null>(null);
const open = ref(false);
const query = ref('');
const selected = computed(() => props.people.find((person) => person.id === props.selectedId));
const matches = computed(() => props.people.filter((person) => person.name.toLocaleLowerCase('ru-RU').includes(query.value.trim().toLocaleLowerCase('ru-RU'))));
async function toggle() {
    open.value = !open.value;
    if (open.value) { query.value = ''; await nextTick(); input.value?.focus(); }
}
function choose(id: number) { emit('select', id); open.value = false; }
function closeOutside(event: MouseEvent) { if (!root.value?.contains(event.target as Node)) open.value = false; }
onMounted(() => document.addEventListener('mousedown', closeOutside));
onBeforeUnmount(() => document.removeEventListener('mousedown', closeOutside));
</script>

<template>
    <div ref="root" class="task-employee-select">
        <button type="button" class="task-employee-trigger" :aria-expanded="open" @click="toggle"><span>{{ selected?.name ?? 'Выберите сотрудника' }}</span><span aria-hidden="true">⌄</span></button>
        <div v-if="open" class="task-employee-popover" @keydown.esc="open = false"><input ref="input" v-model="query" type="search" aria-label="Поиск сотрудника" placeholder="Поиск по имени…" /><div class="task-employee-options"><button v-for="person in matches" :key="person.id" type="button" :class="{ active: person.id === selectedId }" @click="choose(person.id)"><span class="task-employee-avatar">{{ person.name.slice(0, 1) }}</span>{{ person.name }}<span v-if="person.id === selectedId" class="task-employee-check">✓</span></button><div v-if="!matches.length" class="task-employee-no-match">Сотрудник не найден</div></div></div>
    </div>
</template>
