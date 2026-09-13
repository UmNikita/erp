<template>
    <tr>
        <td>
            <div class="client">
                <div>
                    <router-link class="name" :to="clientUrl(client.id)">
                        {{ client.name }}    
                    </router-link>
                    <span class="company">Канал коммуникации: {{ client.channel ?? '-' }}</span>
                </div>
            </div>
        </td>
        <td>
            <div class="contacts">
                <div class="contact">{{ client.phone ? formatPhone(client.phone) : '-' }}</div>
                <div class="contact">{{ client.email ?? '-' }}</div>
            </div>
        </td>
        <td>
            <div class="deals">
                <strong>{{ client.leads_count }} {{ getPluralizeLead(client.leads_count) }}</strong>
                <span>{{ formatAmount(client.leads_amount) }} ₽</span>
            </div>
        </td>
        <td>
            <span class="date">{{ formatResponseDate(client.date_create) }}</span>
        </td>
        <td>
            <div class="actions">
                <router-link class="icon-btn" :to="clientUrl(client.id)">
                    <svg viewBox="0 0 24 24" fill="none"><path d="m9 5 7 7-7 7" stroke="currentColor" stroke-width="1.8"/></svg>
                </router-link>
                <button class="pipeline-settings__delete" @click="deleteClient">
                    <DeleteIco />
                </button>
                
            </div>
        </td>
    </tr>
</template>

<script setup lang="ts">
    import { clientUrl } from '../../../routes/client.ts';
    import { Client } from '../../../types/client.ts';
    import { formatAmount, formatPhone, formatResponseDate } from '../../../utils/fields.ts';
    import { getPluralizeLead } from '../../../utils/words.ts';
    import DeleteIco from '../../icons/DeleteIco.vue';
    import {deleteClient as deleteClientApi} from '../../../api/client.ts';
    import { useClientTableStore } from '../../../stores/clientTable.ts';
    import { ref } from 'vue';

    const clientTableStore = useClientTableStore();
    const accepting = ref(false);

    const props = defineProps<{
        client: Client;
    }>();

    async function deleteClient() {
        if(accepting.value)
            return;
        accepting.value = true;
        if(props.client.leads_count && props.client.leads_count > 0) {
            alert("Нельзя удалить клиента со сделками!");
            return;
        }

        if(!confirm("Вы действительно хотите удалить?"))
            return;
        try {
            await deleteClientApi(props.client.id);
            clientTableStore.refresh();

        } catch (err) {
            alert("Возникла ошибка");
            return;
        } finally {
            accepting.value = false;
        }
    }

</script>

<style scoped>
    .table tr {
        border-bottom: 1px solid #edf0f4;
    }
    .pipeline-settings__delete {
        width: 42px;
        height: 42px;
        color: #9a6266;
        flex: 0 0 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff4f4;
        border: 1px solid #f2d9db;
        border-radius: 9px;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .pipeline-settings__delete:hover {
        color: #ffffff;
        background: #e5484d;
        border-color: #e5484d;
    }

    .pipeline-settings__delete svg {
        width: 20px;
        height: 20px;
    }
    .table td {
        padding: 16px;
        border-bottom: 1px solid #edf0f4;
        vertical-align: middle;
    }

    .table tr:last-child td {
        border-bottom: 0;
    }

    .table tbody tr:hover {
        background: #fbfcfe;
    }

    .client {
        display: flex;
        align-items: center;
        gap: 13px;
        min-width: 0;
    }

    .name {
        display: block;
        overflow: hidden;
        color: var(--text);
        font-size: 14px;
        font-weight: 650;
        text-decoration: none;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .name:hover {
        color: var(--blue);
    }

    .company {
        display: block;
        margin-top: 4px;
        color: var(--muted);
        font-size: 12px;
    }

    .contacts {
        display: grid;
        gap: 6px;
    }

    .contact {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #424b5b;
        font-size: 13px;
    }

    .deals strong {
        display: block;
        font-size: 14px;
    }

    .deals span {
        color: #566174;
        font-size: 13px;
    }

    .date {
        font-size: 13px;
        color: #566174;
    }

    .actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .icon-btn {
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
        border: 1px solid var(--line);
        border-radius: 9px;
        background: #fff;
        color: #5e697c;
        text-decoration: none;
        cursor: pointer;
    }

    .icon-btn:hover {
        color: var(--blue);
        background: var(--soft);
    }

    .icon-btn svg {
        width: 18px;
    }

</style>