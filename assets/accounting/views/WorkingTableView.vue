<script setup lang="ts">
import { computed, reactive, ref } from 'vue';
import { useAccountingStore } from '../stores/accounting';
import type { AccountingOperation, OperationType } from '../types';

const store = useAccountingStore();
const filtersOpen = ref(false);
const filters = reactive({
    from: store.period.from,
    to: store.period.to,
    type: 'all' as 'all' | OperationType,
    categoryId: 0,
    authorId: 0,
    allocation: 'all' as 'all' | 'allocated' | 'unallocated',
    included: 'all' as 'all' | 'yes' | 'no',
    min: null as number | null,
    max: null as number | null,
    search: '',
});

const filteredOperations = computed(() => store.operations.filter((operation) => {
    const categoryMatch = !filters.categoryId || operation.allocations.some((item) => item.categoryId === filters.categoryId);
    const authorMatch = !filters.authorId || operation.createdBy?.id === filters.authorId;
    const allocated = store.remaining(operation) <= 0.001;
    const allocationMatch = filters.allocation === 'all' || (filters.allocation === 'allocated' ? allocated : !allocated);
    const includedMatch = filters.included === 'all' || (filters.included === 'yes' ? operation.included : !operation.included);
    const text = `${operation.counterparty} ${operation.comment || ''}`.toLowerCase();
    return (filters.type === 'all' || operation.type === filters.type)
        && categoryMatch && authorMatch && allocationMatch && includedMatch
        && (filters.min === null || operation.amount >= filters.min)
        && (filters.max === null || operation.amount <= filters.max)
        && (!filters.search || text.includes(filters.search.toLowerCase()));
}));

const visibleIncome = computed(() => filteredOperations.value.filter((item) => item.included && item.type === 'income').reduce((sum, item) => sum + item.amount, 0));
const visibleExpense = computed(() => filteredOperations.value.filter((item) => item.included && item.type === 'expense').reduce((sum, item) => sum + item.amount, 0));
const formatMoney = (value: number) => new Intl.NumberFormat('ru-RU', { maximumFractionDigits: 2 }).format(value) + ' ₽';

function categoryId(operation: AccountingOperation): number {
    return operation.allocations[0]?.categoryId ?? 0;
}

function categoryName(operation: AccountingOperation): string {
    return store.categories.find((category) => category.id === categoryId(operation))?.name ?? 'Не распределено';
}

async function changePeriod(): Promise<void> {
    await store.load(filters.from, filters.to);
}

async function patch(operation: AccountingOperation, field: string, value: unknown): Promise<void> {
    await store.updateOperation(operation.id, { [field]: value });
}

function exportCsv(): void {
    const rows = [
        ['Дата', 'Тип', 'Сумма', 'Контрагент', 'Статья', 'Комментарий', 'Добавил', 'В итогах'],
        ...filteredOperations.value.map((operation) => [
            operation.date,
            operation.type === 'income' ? 'Доход' : 'Расход',
            operation.amount,
            operation.counterparty,
            categoryName(operation),
            operation.comment || '',
            operation.createdBy?.name || '',
            operation.included ? 'Да' : 'Нет',
        ]),
    ];
    const csv = rows.map((row) => row.map((cell) => `"${String(cell).replaceAll('"', '""')}"`).join(';')).join('\n');
    const link = document.createElement('a');
    link.href = URL.createObjectURL(new Blob([`\ufeff${csv}`], { type: 'text/csv;charset=utf-8' }));
    link.download = `accounting-${filters.from}-${filters.to}.csv`;
    link.click();
    URL.revokeObjectURL(link.href);
}
</script>

<template>
    <section class="table-page page-shell">
        <div class="table-toolbar">
            <button class="button outline" type="button" @click="filtersOpen = !filtersOpen">{{ filtersOpen ? 'Скрыть' : 'Показать' }}</button>
            <button class="button outline" type="button" @click="exportCsv">Экспорт CSV</button>
        </div>

        <div v-if="filtersOpen" class="filters-panel">
            <label><span>С</span><input v-model="filters.from" type="date" @change="changePeriod"></label>
            <label><span>По</span><input v-model="filters.to" type="date" @change="changePeriod"></label>
            <label><span>Тип</span><select v-model="filters.type"><option value="all">Все</option><option value="income">Доход</option><option value="expense">Расход</option></select></label>
            <label><span>Статья</span><select v-model.number="filters.categoryId"><option :value="0">Все</option><option v-for="category in store.categories" :key="category.id" :value="category.id">{{ category.name }}</option></select></label>
            <label><span>Кто добавил</span><select v-model.number="filters.authorId"><option :value="0">Все</option><option v-for="user in store.users" :key="user.id" :value="user.id">{{ user.name }}</option></select></label>
            <label><span>Распределение</span><select v-model="filters.allocation"><option value="all">Все</option><option value="allocated">Распределено</option><option value="unallocated">Не распределено</option></select></label>
            <label><span>В итогах</span><select v-model="filters.included"><option value="all">Все</option><option value="yes">Да</option><option value="no">Нет</option></select></label>
            <label><span>Сумма от</span><input v-model.number="filters.min" type="number" min="0"></label>
            <label><span>Сумма до</span><input v-model.number="filters.max" type="number" min="0"></label>
            <label class="filter-search"><span>Поиск</span><input v-model="filters.search" placeholder="Контрагент или комментарий"></label>
        </div>

        <div class="table-summary">
            <div><small>Доход</small><strong class="income">{{ formatMoney(visibleIncome) }}</strong></div>
            <div><small>Расход</small><strong class="expense">{{ formatMoney(visibleExpense) }}</strong></div>
            <div><small>Итого</small><strong>{{ formatMoney(visibleIncome - visibleExpense) }}</strong></div>
        </div>

        <div class="section-row"><strong>Операции</strong><span>Показано: {{ filteredOperations.length }}</span></div>
        <div class="data-table-wrap">
            <table class="data-table">
                <thead><tr><th>Дата</th><th>Тип</th><th>Сумма</th><th>От кого / кому</th><th>Статья</th><th>Комментарий</th><th>Добавил</th><th>В итогах</th></tr></thead>
                <tbody>
                    <tr v-for="operation in filteredOperations" :key="operation.id">
                        <td><input :value="operation.date" type="date" @change="patch(operation, 'date', ($event.target as HTMLInputElement).value)"></td>
                        <td><select :value="operation.type" @change="patch(operation, 'type', ($event.target as HTMLSelectElement).value)"><option value="income">Доход</option><option value="expense">Расход</option></select></td>
                        <td><input class="money-input" :value="operation.amount" type="number" min="0.01" step="0.01" @change="patch(operation, 'amount', Number(($event.target as HTMLInputElement).value))"></td>
                        <td><input :value="operation.counterparty" @change="patch(operation, 'counterparty', ($event.target as HTMLInputElement).value)"></td>
                        <td><select :value="categoryId(operation)" @change="patch(operation, 'categoryId', Number(($event.target as HTMLSelectElement).value) || null)"><option :value="0">Не распределено</option><option v-for="category in store.activeCategories.filter(item => item.type === operation.type)" :key="category.id" :value="category.id">{{ category.icon }} {{ category.name }}</option></select></td>
                        <td><input :value="operation.comment || ''" @change="patch(operation, 'comment', ($event.target as HTMLInputElement).value)"></td>
                        <td><select :value="operation.createdBy?.id || 0" @change="patch(operation, 'createdById', Number(($event.target as HTMLSelectElement).value) || null)"><option :value="0">Не указан</option><option v-for="user in store.users" :key="user.id" :value="user.id">{{ user.name }}</option></select></td>
                        <td class="check-cell"><input :checked="operation.included" type="checkbox" @change="patch(operation, 'included', ($event.target as HTMLInputElement).checked)"></td>
                    </tr>
                    <tr v-if="!filteredOperations.length"><td colspan="8" class="empty-table">Операций по выбранным условиям нет</td></tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
