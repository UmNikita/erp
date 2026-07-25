<template>
    <div class="stage">
        <div class="color"></div>
        <p>{{stage.name}}</p>
        <p>{{stage.leadCount}} {{getPluralizeLead(stage.leadCount)}}</p>
        <p>{{stage.moneyAmount}} ₽</p>
        <Lead v-for="lead in stage?.leads" :key="lead.id" :lead="lead" />
    </div>
</template>

<script setup lang="ts">
    import { Stage } from '../../types/stage';
    import { getPluralizeLead } from '../../utils/words';
    import Lead from './Lead.vue';

    defineProps<{stage: Stage}>();
</script>

<style>
    .stage {
        width: 280px;
        background: #f5f5f5;
        border: 1px solid gray;
        border-radius: 8px;
        padding: 15px;
        margin: 10px;
    }

    .color {
        width: 100%;
        height: 5px;
        background: #78BC61;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .stage > p {
        margin: 5px 0;
        font-size: 14px;
    }

    .stage > p:first-of-type {
        font-weight: bold;
        font-size: 18px;
    }
</style>