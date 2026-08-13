<template>
    <article class="mail">
        <div class="mail-icon">✉</div>
        <div>
            <strong>{{ email.title }}</strong>
            <span>{{ formatResponseDate(email.date) }} · {{ email.managerName }}</span>
        </div>
        <span class="sent">Отправлено</span>
    </article>
</template>

<script setup lang="ts">
    import { EmailHistory } from '../../../../../types/client';
    import { formatResponseDate } from '../../../../../utils/fields';

    const props = defineProps<{
        email: EmailHistory;
    }>();

</script>

<style scoped>
    .mail {
        display: grid;
        grid-template-columns: 42px minmax(0, 1fr) auto;
        align-items: center;
        gap: 14px;
        padding: 16px;
        border: 1px solid var(--line);
        border-radius: 11px;
    }

    .mail-icon {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        border-radius: 10px;
        background: var(--soft);
        color: var(--blue);
    }

    .mail-icon svg {
        width: 20px;
    }

    .mail strong {
        display: block;
        overflow: hidden;
        margin-bottom: 5px;
        font-size: 14px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .mail span {
        color: var(--muted);
        font-size: 12px;
    }

    .sent {
        height: 27px;
        display: inline-flex;
        align-items: center;
        padding: 0 9px;
        border-radius: 999px;
        color: #198b50 !important;
        background: var(--greenSoft);
        font-size: 11px !important;
        font-weight: 650;
    }
</style>