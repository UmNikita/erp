<template>
    <div class="item">
        <div class="item-icon">₽</div>
        <div class="item-text">
            <strong>{{ lead.name }}</strong>
            <span>{{ lead.stage.pipeline.name }} · {{ lead.stage.name }}</span>
        </div>
        <span class="amount">{{ formatAmount(lead.budget) }} ₽</span>
    </div>
</template>

<script setup lang="ts">
    import { LeadResponse } from '../../../../../types/lead';
    import { formatAmount } from '../../../../../utils/fields';

    const props = defineProps<{
        lead: LeadResponse;
    }>();

</script>

<style scoped>
    .item {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 13px;
        border: 1px solid #edf0f4;
        border-radius: 10px;
        background: #fbfcfd;
    }

    .item-icon {
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
        flex: 0 0 36px;
        border-radius: 9px;
        background: var(--soft);
        color: var(--blue);
    }

    .item-icon svg {
        width: 18px;
    }

    .item-text {
        min-width: 0;
        flex: 1;
    }

    .item-text strong {
        display: block;
        overflow: hidden;
        font-size: 13px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .item-text span {
        display: block;
        margin-top: 4px;
        color: var(--muted);
        font-size: 12px;
    }

    .amount {
        font-size: 13px;
        font-weight: 650;
    }
</style>