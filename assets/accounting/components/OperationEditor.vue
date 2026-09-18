<script setup lang="ts">
import { reactive } from 'vue';
import type { AccountingOperation, OperationPayload, OperationType } from '../types';

const props = defineProps<{ operation?: AccountingOperation | null; busy?: boolean }>();
const emit = defineEmits<{ save: [payload: OperationPayload]; cancel: [] }>();

const today = new Date().toISOString().slice(0, 10);
const form = reactive<OperationPayload>({
    type: props.operation?.type ?? 'income',
    amount: props.operation?.amount ?? 0,
    date: props.operation?.date ?? today,
    counterparty: props.operation?.counterparty ?? '',
    comment: props.operation?.comment ?? '',
    included: props.operation?.included ?? true,
});

function chooseType(type: OperationType): void {
    form.type = type;
}

function submit(): void {
    if (!form.amount || !form.date || !form.counterparty.trim()) return;
    emit('save', { ...form, counterparty: form.counterparty.trim(), comment: form.comment?.trim() || null });
}
</script>

<template>
    <form class="inline-editor operation-editor" @submit.prevent="submit">
        <div class="editor-head">
            <span class="editor-plus">+</span>
            <strong>{{ operation ? 'Редактирование операции' : 'Новая операция' }}</strong>
            <small>Заполните сверху вниз</small>
        </div>

        <div class="type-switch">
            <button type="button" :class="{ active: form.type === 'income' }" @click="chooseType('income')">↗ Доход</button>
            <button type="button" :class="{ active: form.type === 'expense' }" @click="chooseType('expense')">↘ Расход</button>
        </div>

        <label class="editor-field">
            <span>Сумма, ₽</span>
            <input v-model.number="form.amount" min="0.01" step="0.01" type="number" required>
        </label>
        <label class="editor-field">
            <span>Дата</span>
            <input v-model="form.date" type="date" required>
        </label>
        <label class="editor-field">
            <span>От кого / кому</span>
            <input v-model="form.counterparty" maxlength="180" placeholder="Имя или компания" required>
        </label>
        <label class="editor-field editor-textarea">
            <span>Комментарий</span>
            <textarea v-model="form.comment" maxlength="1000" placeholder="За что поступили или ушли деньги" />
        </label>
        <label class="editor-check">
            <input v-model="form.included" type="checkbox">
            <span>Учитывать операцию в итогах</span>
        </label>

        <div class="editor-actions">
            <button class="button muted" type="button" @click="emit('cancel')">Отмена</button>
            <button class="button primary" type="submit" :disabled="busy">✓ {{ operation ? 'Сохранить' : 'Создать' }}</button>
        </div>
    </form>
</template>
