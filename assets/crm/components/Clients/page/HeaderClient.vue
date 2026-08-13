<template>
    <router-link class="back" :to="clientTableUrl(1)">
        <svg viewBox="0 0 24 24" fill="none"><path d="m15 5-7 7 7 7" stroke="currentColor" stroke-width="1.8"/></svg>
        Назад к клиентам
    </router-link>

    <header class="header">
        <div class="profile">
            <div>
                <h1>{{client?.name}}</h1>
                <p>Дата создания: {{ client?.date_create ? formatResponseDate(client.date_create) : '-' }}</p>
            </div>
        </div>
        <div class="actions">
            <button class="btn btn-secondary">Запланировать (временно неактивно)</button>
            <button class="btn btn-primary">+ Создать задачу (временно неактивно)</button>
        </div>
    </header>
</template>

<script setup lang="ts">
    import { useCurrentClient } from '../../../composables/Clients/useCurrentClient';
    import { clientTableUrl } from '../../../routes/client';
    import { formatResponseDate } from '../../../utils/fields';

    const { client } = useCurrentClient();

</script>

<style scoped>
    .back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 22px;
        color: #637086;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }

    .back:hover {
        color: var(--blue);
    }

    .back svg {
        width: 18px;
    }

    .header {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        margin-bottom: 24px;
    }

    .profile {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
    }

    .profile h1 {
        margin: 0 0 6px;
        font-size: 28px;
    }

    .profile p {
        margin: 0 0 9px;
        color: var(--muted);
        font-size: 14px;
    }

    .actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .btn-secondary {
        border: 1px solid #d6dce5;
        background: #fff;
        color: #343b48;
    }

    .btn-secondary:hover {
        background: #f7f8fa;
    }
</style>