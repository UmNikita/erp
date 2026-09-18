<script setup lang="ts">
import { computed, ref } from 'vue';
import OperationEditor from '../components/OperationEditor.vue';
import CategoryEditor from '../components/CategoryEditor.vue';
import { useAccountingStore } from '../stores/accounting';
import type { AccountingOperation, Category, CategoryPayload, OperationPayload, OperationType } from '../types';

const store = useAccountingStore();
const createOpen = ref(false);
const editingOperationId = ref<number | null>(null);
const editingCategories = ref(false);
const createCategoryOpen = ref(false);
const editingCategoryId = ref<number | null>(null);
const selectedCategoryId = ref<number | null>(null);
const draggedOperationId = ref<number | null>(null);
const categoryFilter = ref<'all' | OperationType>('all');
const from = ref(store.period.from);
const to = ref(store.period.to);

const unallocated = computed(() => store.operations.filter((operation) => store.remaining(operation) > 0.001));
const selectedCategory = computed(() => store.categories.find((category) => category.id === selectedCategoryId.value) ?? null);
const selectedOperations = computed(() => selectedCategory.value
    ? store.operations.filter((operation) => operation.allocations.some((allocation) => allocation.categoryId === selectedCategory.value?.id))
    : []);
const visibleCategories = computed(() => {
    const list = editingCategories.value ? store.categories : store.activeCategories;
    return list.filter((category) => categoryFilter.value === 'all' || category.type === categoryFilter.value);
});
const operationBeingEdited = computed(() => store.operations.find((operation) => operation.id === editingOperationId.value) ?? null);
const categoryBeingEdited = computed(() => store.categories.find((category) => category.id === editingCategoryId.value) ?? null);

const formatMoney = (value: number) => new Intl.NumberFormat('ru-RU', { maximumFractionDigits: 2 }).format(value) + ' ₽';

async function changePeriod(): Promise<void> {
    await store.load(from.value, to.value);
}

async function saveOperation(payload: OperationPayload): Promise<void> {
    if (editingOperationId.value) await store.updateOperation(editingOperationId.value, payload);
    else await store.createOperation(payload);
    editingOperationId.value = null;
    createOpen.value = false;
}

async function saveCategory(payload: CategoryPayload): Promise<void> {
    if (editingCategoryId.value) await store.updateCategory(editingCategoryId.value, payload);
    else await store.createCategory(payload);
    editingCategoryId.value = null;
    createCategoryOpen.value = false;
}

function startDrag(operation: AccountingOperation): void {
    draggedOperationId.value = operation.id;
}

async function dropOn(category: Category): Promise<void> {
    const operation = store.operations.find((item) => item.id === draggedOperationId.value);
    draggedOperationId.value = null;
    if (!operation || operation.type !== category.type || !category.active) return;
    await store.allocateOperation(operation, category.id);
}

function openCategory(category: Category): void {
    if (editingCategories.value) editingCategoryId.value = category.id;
    else selectedCategoryId.value = category.id;
}

function editOperation(operation: AccountingOperation): void {
    editingOperationId.value = operation.id;
    createOpen.value = false;
}

async function toggleArchive(category: Category): Promise<void> {
    await store.updateCategory(category.id, { active: !category.active });
    editingCategoryId.value = null;
}

async function removeCategory(category: Category): Promise<void> {
    if (!window.confirm(`Удалить статью «${category.name}»?`)) return;
    await store.deleteCategory(category.id);
    editingCategoryId.value = null;
}
</script>

<template>
    <section class="workspace-page page-shell">
        <div class="workspace-toolbar">
            <div class="period-control">
                <label>С <input v-model="from" type="date" @change="changePeriod"></label>
                <label>по <input v-model="to" type="date" @change="changePeriod"></label>
            </div>
            <button class="button outline" type="button" @click="editingCategories = !editingCategories; selectedCategoryId = null">
                {{ editingCategories ? 'Готово' : 'Статьи' }}
            </button>
        </div>

        <div class="workspace-columns">
            <section class="operation-column">
                <div class="column-heading">
                    <div><strong>Не распределено</strong><span class="count-pill">{{ unallocated.length }}</span></div>
                    <small>Операции, ожидающие статьи</small>
                </div>
                <div class="remaining-row"><span>Осталось распределить</span><strong>{{ formatMoney(unallocated.reduce((sum, item) => sum + store.remaining(item), 0)) }}</strong></div>

                <OperationEditor
                    v-if="createOpen"
                    :busy="store.saving"
                    @save="saveOperation"
                    @cancel="createOpen = false"
                />
                <button v-else class="add-pill" type="button" @click="createOpen = true; editingOperationId = null">＋ Добавить операцию</button>

                <div class="scroll-list operation-list">
                    <template v-for="operation in unallocated" :key="operation.id">
                        <OperationEditor
                            v-if="editingOperationId === operation.id"
                            :operation="operation"
                            :busy="store.saving"
                            @save="saveOperation"
                            @cancel="editingOperationId = null"
                        />
                        <article
                            v-else
                            class="operation-pill"
                            draggable="true"
                            @dragstart="startDrag(operation)"
                        >
                            <span class="direction" :class="operation.type">{{ operation.type === 'income' ? '↗' : '↘' }}</span>
                            <strong :class="operation.type">{{ operation.type === 'income' ? '+' : '−' }}{{ formatMoney(store.remaining(operation)) }}</strong>
                            <span>{{ operation.counterparty ? `от ${operation.counterparty}` : 'контрагент не указан' }}</span>
                            <button type="button" aria-label="Редактировать" @click="editOperation(operation)">•••</button>
                        </article>
                    </template>
                    <div v-if="!unallocated.length" class="empty-state">Все операции распределены</div>
                </div>
            </section>

            <section class="category-column">
                <template v-if="selectedCategory">
                    <div class="category-detail-head">
                        <article class="category-pill selected" :style="{ '--category-color': selectedCategory.color }">
                            <span class="category-icon">{{ selectedCategory.icon }}</span>
                            <strong>{{ selectedCategory.name }}</strong>
                            <small>{{ selectedCategory.type === 'income' ? 'Доход' : 'Расход' }}</small>
                            <b>{{ formatMoney(selectedOperations.reduce((sum, item) => sum + item.allocations.filter(a => a.categoryId === selectedCategory?.id).reduce((s, a) => s + a.amount, 0), 0)) }}</b>
                        </article>
                        <button class="button primary back-button" type="button" @click="selectedCategoryId = null">← Назад</button>
                    </div>
                    <div class="detail-label"><span>Операции</span><small>{{ selectedOperations.length }} операций</small></div>
                    <div class="scroll-list detail-operations">
                        <template v-for="operation in selectedOperations" :key="operation.id">
                            <OperationEditor
                                v-if="editingOperationId === operation.id"
                                :operation="operation"
                                :busy="store.saving"
                                @save="saveOperation"
                                @cancel="editingOperationId = null"
                            />
                            <article v-else class="operation-pill">
                                <span class="direction" :class="operation.type">{{ operation.type === 'income' ? '↗' : '↘' }}</span>
                                <strong :class="operation.type">{{ operation.type === 'income' ? '+' : '−' }}{{ formatMoney(operation.amount) }}</strong>
                                <span>{{ operation.counterparty ? `от ${operation.counterparty}` : 'контрагент не указан' }}</span>
                                <button type="button" aria-label="Редактировать" @click="editOperation(operation)">•••</button>
                            </article>
                        </template>
                        <div v-if="!selectedOperations.length" class="empty-state">В этой статье пока нет операций</div>
                    </div>
                </template>

                <template v-else>
                    <div class="category-topline">
                        <div class="column-heading">
                            <strong>{{ editingCategories ? 'Редактирование статей' : 'Статьи распределения' }}</strong>
                            <small>{{ editingCategories ? 'Нажмите на статью, чтобы изменить её' : 'Все статьи за выбранный период' }}</small>
                        </div>
                        <div v-if="!editingCategories" class="segmented-filter">
                            <button :class="{ active: categoryFilter === 'all' }" @click="categoryFilter = 'all'">Все</button>
                            <button :class="{ active: categoryFilter === 'income' }" @click="categoryFilter = 'income'">Доходы</button>
                            <button :class="{ active: categoryFilter === 'expense' }" @click="categoryFilter = 'expense'">Расходы</button>
                        </div>
                    </div>

                    <CategoryEditor
                        v-if="editingCategories && createCategoryOpen"
                        :busy="store.saving"
                        @save="saveCategory"
                        @cancel="createCategoryOpen = false"
                    />
                    <CategoryEditor
                        v-if="editingCategories && categoryBeingEdited"
                        :category="categoryBeingEdited"
                        :busy="store.saving"
                        @save="saveCategory"
                        @cancel="editingCategoryId = null"
                        @archive="toggleArchive(categoryBeingEdited)"
                        @remove="removeCategory(categoryBeingEdited)"
                    />
                    <button v-if="editingCategories && !createCategoryOpen && !categoryBeingEdited" class="add-pill wide" type="button" @click="createCategoryOpen = true">＋ Добавить статью</button>

                    <div class="scroll-list category-list" :class="{ editing: editingCategories }">
                        <article
                            v-for="category in visibleCategories"
                            :key="category.id"
                            class="category-pill"
                            :class="{ archived: !category.active }"
                            :style="{ '--category-color': category.color }"
                            @click="openCategory(category)"
                            @dragover.prevent
                            @drop.prevent="dropOn(category)"
                        >
                            <span class="category-icon">{{ category.icon }}</span>
                            <strong>{{ category.name }}</strong>
                            <small>{{ category.type === 'income' ? 'Доход' : 'Расход' }}{{ category.active ? '' : ' · архив' }}</small>
                            <b>{{ formatMoney(store.operations.reduce((sum, operation) => sum + operation.allocations.filter(a => a.categoryId === category.id).reduce((s, a) => s + a.amount, 0), 0)) }}</b>
                        </article>
                    </div>
                </template>
            </section>
        </div>

        <footer class="totals-bar">
            <div><span class="total-icon income">↗</span><small>Доход</small><strong>{{ formatMoney(store.totalIncome) }}</strong></div>
            <div><span class="total-icon expense">↘</span><small>Расход</small><strong>{{ formatMoney(store.totalExpense) }}</strong></div>
            <div><span class="total-icon balance">=</span><small>Итого</small><strong>{{ formatMoney(store.balance) }}</strong></div>
            <span class="totals-period">{{ from }} — {{ to }}</span>
        </footer>
    </section>
</template>
