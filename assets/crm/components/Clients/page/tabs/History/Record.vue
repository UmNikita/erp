<template>
    <div class="history-row">
        <time>{{ formatResponseDate(record.date) }}</time>
        <div class="history-icon">*</div>
        <div>
            <strong>{{ record.message }}</strong>
            <span>{{ record.managerName }}</span>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { ClientHistory } from '../../../../../types/client';
    import { formatResponseDate } from '../../../../../utils/fields';

    const props = defineProps<{
        record: ClientHistory;
    }>();

</script>

<style scoped>
   .history-row {
        display: grid;
        grid-template-columns: 90px 38px 1fr;
        gap: 14px;
        align-items: start;
        padding: 12px 0;
        border-bottom: 1px solid #edf0f4;
    }

    .history-row time {
        padding-top: 5px;
        color: var(--muted);
        font-size: 12px;
    }

    .history-row strong {
        display: block;
        margin-bottom: 4px;
        font-size: 13px;
    }

    .history-row span {
        color: var(--muted);
        font-size: 12px;
    }

    .history-icon {
        width: 34px;
        height: 34px;
        display: grid;
        place-items: center;
        border-radius: 9px;
        background: var(--soft);
        color: var(--blue);
    }

    .history-icon svg {
        width: 17px;
    }
</style>