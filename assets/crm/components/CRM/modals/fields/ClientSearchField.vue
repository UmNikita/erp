<template>
    <FieldWrapper :required="false" title="Клиент" :error="error" :ico="LeadIco">
        <input v-model="search" type="text" placeholder="Введите имя, email или телефон"
            @input="onSearch" @focus="showResults = true" @blur="hideResults" />

        <div v-if="showResults && clients.length" class="client-results">
            <div v-for="client in clients" :key="client.id"
                class="client-item" @click="select(client)">
                <div>{{ client.name }}</div>
                <small>
                    {{ client.email }}
                    {{ client.phone }}
                </small>
            </div>
        </div>
    </FieldWrapper>
</template>

<script setup lang="ts">
    import { ref } from 'vue';
    import FieldWrapper from './FieldWrapper.vue';
    import { searchClients } from '../../../../api/client';
    import LeadIco from '../../../icons/Kanban/ClientIco.vue';

    interface Client {
        id: number;
        name: string;
        email?: string;
        phone?: string;
    }

    const props = defineProps<{
        error?: string;
    }>();

    const emit = defineEmits<{
        select: [client: Client]
    }>();

    const search = ref('');
    const clients = ref<Client[]>([]);
    const showResults = ref(false);

    let timeout: ReturnType<typeof setTimeout> | null = null;

    async function onSearch() {
        if (timeout) {
            clearTimeout(timeout);
        }

        timeout = setTimeout(async () => {
            if (search.value.length < 3) {
                clients.value = [];
                return;
            }

            clients.value = await searchClients(search.value);
        }, 300);
    }

    function select(client: Client) {
        emit('select', client);

        search.value = client.name;
        clients.value = [];
    }

    function hideResults() {
        setTimeout(() => {
            showResults.value = false;
        }, 100);
    }
</script>

<style>
    .client-results {
        width: 400px;
        max-height: 250px;
        overflow-y: auto;

        position: absolute;
        margin-top: 50px;

        background: #ffffff;
        border: 1px solid #dfe3e9;
        border-radius: 10px;

        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);

        z-index: 1000;
    }

    .client-item {
        padding: 12px 16px;
        cursor: pointer;

        transition: background-color 0.15s ease;
    }

    .client-item:hover {
        background-color: #f5f7fa;
    }

    .client-item div {
        font-size: 14px;
        font-weight: 500;
        color: #171b24;
        margin-bottom: 4px;
    }

    .client-item small {
        font-size: 12px;
        color: #6b7280;
    }

    .client-item + .client-item {
        border-top: 1px solid #eef1f5;
    }
</style>