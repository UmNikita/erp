<script setup lang="ts">
import { computed, ref } from 'vue';
import { useAccountingStore } from '../stores/accounting';
import type { AuditEntry } from '../types';

const store = useAccountingStore();
const search = ref('');
const restoringId = ref<number | null>(null);
const actionLabels: Record<string, string> = {
    'transaction.created': 'Создана операция',
    'transaction.updated': 'Изменена операция',
    'transaction.allocated': 'Операция распределена',
    'transaction.unallocated': 'Распределение снято',
    'transaction.deleted': 'Операция удалена',
    'transaction.restored': 'Операция восстановлена',
    'category.created': 'Создана статья',
    'category.updated': 'Изменена статья',
    'category.deleted': 'Удалена статья',
};

const entries = computed(() => store.audit.filter((entry) => {
    const text = `${entry.user?.name || ''} ${actionLabels[entry.action] || entry.action} ${entry.entityType} ${entry.entityId || ''} ${JSON.stringify(entry.payload)}`.toLowerCase();
    return !search.value || text.includes(search.value.toLowerCase());
}));

const formatDate = (value: string) => new Intl.DateTimeFormat('ru-RU', { dateStyle: 'short', timeStyle: 'medium' }).format(new Date(value));

async function restore(entry: AuditEntry): Promise<void> {
    if (!window.confirm('Восстановить удалённую операцию со всеми её данными и распределением?')) return;
    restoringId.value = entry.id;
    try {
        await store.restoreAuditEntry(entry.id);
    } finally {
        restoringId.value = null;
    }
}
</script>

<template>
    <section class="audit-page page-shell">
        <div class="audit-toolbar">
            <input v-model="search" placeholder="Поиск по журналу">
            <button class="button outline" type="button" @click="store.reloadAudit">Обновить</button>
        </div>
        <div class="section-row"><strong>Действия</strong><span>Показано: {{ entries.length }}</span></div>
        <div class="audit-list">
            <article v-for="entry in entries" :key="entry.id" class="audit-row">
                <time>{{ formatDate(entry.createdAt) }}</time>
                <strong>{{ entry.user?.name || 'Система' }}</strong>
                <span>{{ actionLabels[entry.action] || entry.action }}</span>
                <small>{{ entry.entityType }} #{{ entry.entityId || '—' }}</small>
                <code>{{ JSON.stringify(entry.payload) }}</code>
                <div class="audit-actions">
                    <button
                        v-if="entry.action === 'transaction.deleted' && !entry.restoredAt"
                        type="button"
                        :disabled="restoringId === entry.id"
                        @click="restore(entry)"
                    >{{ restoringId === entry.id ? 'Восстанавливаем…' : 'Восстановить' }}</button>
                    <span v-else-if="entry.action === 'transaction.deleted' && entry.restoredAt">Восстановлено</span>
                </div>
            </article>
            <div v-if="!entries.length" class="empty-state">Записей не найдено</div>
        </div>
    </section>
</template>
