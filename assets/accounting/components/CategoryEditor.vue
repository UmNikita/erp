<script setup lang="ts">
import { reactive } from 'vue';
import type { Category, CategoryPayload, OperationType } from '../types';

const props = defineProps<{ category?: Category | null; busy?: boolean }>();
const emit = defineEmits<{
    save: [payload: CategoryPayload];
    cancel: [];
    archive: [];
    remove: [];
}>();

const icons = ['🌐', '📣', '🤝', '💬', '🖥️', '👥', '🧩', '🏢', '📦', '🚚', '✈️', '✨'];
const colors = ['#3f7af6', '#16b8d9', '#18b77b', '#7ac943', '#f5b52e', '#f28c35', '#ef5a6f', '#d84a8b', '#8b5cf6', '#6554c0', '#60758f', '#b8a67a'];
const form = reactive<CategoryPayload>({
    name: props.category?.name ?? '',
    type: props.category?.type ?? 'income',
    icon: props.category?.icon ?? icons[0],
    color: props.category?.color ?? colors[0],
    active: props.category?.active ?? true,
});

function chooseType(type: OperationType): void { form.type = type; }
function submit(): void {
    if (!form.name.trim()) return;
    emit('save', { ...form, name: form.name.trim() });
}
</script>

<template>
    <form class="inline-editor category-editor" @submit.prevent="submit">
        <div class="editor-head">
            <span class="editor-plus">+</span>
            <strong>{{ category ? 'Настройка статьи' : 'Новая статья' }}</strong>
            <small>Название и оформление</small>
        </div>
        <div class="type-switch">
            <button type="button" :class="{ active: form.type === 'income' }" :disabled="!!category" @click="chooseType('income')">↗ Доход</button>
            <button type="button" :class="{ active: form.type === 'expense' }" :disabled="!!category" @click="chooseType('expense')">↘ Расход</button>
        </div>
        <label class="editor-field">
            <span>Название</span>
            <input v-model="form.name" maxlength="120" placeholder="Например, создание сайтов" required>
        </label>
        <div class="choice-field">
            <span>Смайлик</span>
            <div class="emoji-grid">
                <button v-for="icon in icons" :key="icon" type="button" :class="{ active: form.icon === icon }" @click="form.icon = icon">{{ icon }}</button>
            </div>
        </div>
        <div class="choice-field">
            <span>Цвет</span>
            <div class="color-grid">
                <button v-for="color in colors" :key="color" type="button" :class="{ active: form.color === color }" :style="{ '--choice-color': color }" @click="form.color = color" />
            </div>
        </div>
        <div v-if="category" class="editor-secondary-actions">
            <button type="button" @click="emit('archive')">{{ category.active ? 'Архивировать' : 'Вернуть из архива' }}</button>
            <button v-if="!category.active" class="danger-text" type="button" @click="emit('remove')">Удалить</button>
        </div>
        <div class="editor-actions">
            <button class="button muted" type="button" @click="emit('cancel')">Отмена</button>
            <button class="button primary" type="submit" :disabled="busy">✓ Сохранить</button>
        </div>
    </form>
</template>
