<template>
    <div class="stage" @dragover.prevent @drop="dropLead">
        <button>+ сделка</button>
        <div class="color"></div>
        <p>{{stage.name}}</p>
        <div v-if="stage?.leads.length">    
            <p>{{stage.leadCount}} {{getPluralizeLead(stage.leadCount)}}</p>
            <p>{{stage.moneyAmount}} ₽</p>
            <Lead v-for="lead in stage?.leads" :key="lead.id" :lead="lead" :stage-id="stage.id" />
        </div>
        <EmptyLeads v-else />
    </div>
</template>

<script setup lang="ts">
    import { updateStageLead } from '../../api/lead.ts';
    import { Stage } from '../../types/stage';
    import { getPluralizeLead } from '../../utils/words';
    import EmptyLeads from './EmptyLeads.vue';
    import Lead from './Lead.vue';

    const props = defineProps<{stage: Stage}>();

    const emit = defineEmits<{
        leadDrop: [
            leadId: number,
            fromStageId: number,
            toStageId: number
        ]
    }>();

    async function dropLead(event: DragEvent) {
        const leadId = Number(event.dataTransfer?.getData('leadId'));
        const leadStageId = Number(event.dataTransfer?.getData('stageId'));
        const toStage = props.stage.id;

        if(leadStageId != toStage) {
            emit('leadDrop', leadId, leadStageId, toStage);
            const res = await updateStageLead(leadId, toStage);
            if(res.status == 200) {
            }
            
        }   
    }
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