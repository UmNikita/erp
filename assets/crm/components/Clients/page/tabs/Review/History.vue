<template>
    <div class="activity-row">
        <time>{{ formatResponseDate(record.date) }}</time>
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
    .activity-row {
        display: grid;
        grid-template-columns: 65px 1fr;
        gap: 12px;
    }

    .activity-row time {
        color: var(--muted);
        font-size: 12px;
        text-align: right;
    }

    .activity-row strong {
        display: block;
        font-size: 13px;
    }

    .activity-row span {
        display: block;
        margin-top: 4px;
        color: var(--muted);
        font-size: 12px;
    }
</style>