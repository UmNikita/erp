<template>
    <div class="lead" draggable="true" @dragstart="onDragStart">
        <p>{{lead.name}}</p>
        <p>{{ formatDate(lead.date) }}</p>
        <p>{{lead.client}}</p>
        <p>{{ formatNumber(lead.moneyAmount) }} ₽</p>
        <p>{{lead.manager}}</p>
    </div>
</template>

<script setup lang="ts">
    import { Lead } from '../../types/lead';
    import { formatDate, formatNumber } from '../../utils/fields';
    
    const props = defineProps<{lead: Lead, stageId: number}>();

    function onDragStart(event: DragEvent) {
        event.dataTransfer?.setData('leadId', String(props.lead.id));
        event.dataTransfer?.setData('stageId', String(props.stageId));
    }
</script>

<style>
    .lead {
        background: white;
        border-radius: 6px;
        padding: 12px;
        margin-top: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        cursor: pointer;
    }

    .lead p {
        margin: 4px 0;
        font-size: 13px;
    }

    .lead p:first-child {
        font-weight: bold;
        font-size: 15px;
    }
</style>