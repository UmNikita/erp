<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';
import type { Person } from '../types';
const props = defineProps<{ people: Person[]; selectedIds: number[]; currentUserId: number }>();
const emit = defineEmits<{ change: [ids: number[]] }>();
const open = ref(false);
const query = ref('');
const root = ref<HTMLElement | null>(null);
const people = computed(() => props.people.filter(person => person.name.toLocaleLowerCase('ru').includes(query.value.trim().toLocaleLowerCase('ru'))));
const caption = computed(() => props.selectedIds.length === props.people.length ? 'Все сотрудники' : props.selectedIds.length === 1 && props.selectedIds[0] === props.currentUserId ? 'Только я' : `Сотрудники: ${props.selectedIds.length}`);
function toggle(id: number) { emit('change', props.selectedIds.includes(id) ? props.selectedIds.filter(value => value !== id) : [...props.selectedIds, id]); }
function outside(event: PointerEvent) { if (!root.value?.contains(event.target as Node)) open.value = false; }
function escape(event: KeyboardEvent) { if (event.key === 'Escape') open.value = false; }
onMounted(() => { document.addEventListener('pointerdown', outside); document.addEventListener('keydown', escape); });
onUnmounted(() => { document.removeEventListener('pointerdown', outside); document.removeEventListener('keydown', escape); });
</script>
<template>
    <div ref="root" class="task-user-filter"><button class="task-btn outline" :aria-expanded="open" @click="open = !open">{{ caption }} ▾</button><div v-if="open" class="task-user-popover" role="group" aria-label="Задачи каких пользователей показывать"><strong>Задачи сотрудников</strong><div class="task-user-shortcuts"><button @click="emit('change', props.people.map(person => person.id))">Выбрать всех</button><button @click="emit('change', [currentUserId])">Только я</button></div><input v-model="query" placeholder="Найти сотрудника" aria-label="Найти сотрудника" /><div class="task-user-options"><label v-for="person in people" :key="person.id"><input type="checkbox" :checked="selectedIds.includes(person.id)" @change="toggle(person.id)" />{{ person.name }}</label></div><span v-if="!selectedIds.length" class="task-muted">Никто не выбран — задачи скрыты.</span></div></div>
</template>
