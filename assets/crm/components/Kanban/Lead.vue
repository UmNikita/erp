<template>
    <div class="lead">
        <p>{{lead.name}}</p>
        <p>{{ formatDate(lead.date) }}</p>
        <p>{{lead.client}}</p>
        <p>{{lead.moneyAmount}} ₽</p>
        <p>{{lead.manager}}</p>
    </div>
</template>

<script setup lang="ts">
    import { Lead } from '../../types/lead';
    import { formatDate } from '../../utils/date';
    
    defineProps<{lead: Lead}>();
</script>

<style>
    .lead {
        background: white;
        border-radius: 6px;
        padding: 12px;
        margin-top: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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