<template>
    <div class="pipelines">
        <PipelineBtn v-for="pipeline in pipelines" :key="pipeline.id" :pipeline="pipeline" />
    </div>
    <button>+ этап</button>
    <div v-if="kanban?.stages.length">
        <div class="stages">
            <Stage v-for="stage in kanban?.stages" :key="stage.id" :stage="stage"  @lead-drop="onLeadDrop" />
        </div>
        <div>
            <KanbanStatistic v-if="kanban" :kanban="kanban" />
        </div>
    </div>
    <EmptyStages v-else />
    
</template>

<script setup lang="ts">
    import PipelineBtn from './PipelineBtn.vue';
    import type { Pipeline } from '../../types/pipeline';
    import { onMounted, ref, watch } from 'vue';
    import { Kanban } from '../../types/kanban.ts';
    import { getKanban } from '../../api/kanban.ts';
    import KanbanStatistic from './KanbanStatistic.vue';
    import EmptyStages from './EmptyStages.vue';
    import Stage from './Stage.vue';
    import { updateStageLead } from '../../api/lead.ts';
    import { useRoute } from 'vue-router';

    const props = defineProps<{pipelines: Pipeline[]}>();

    const kanban = ref<Kanban | null>(null);

    const route = useRoute();

    async function loadKanban(id: number) {
        kanban.value = await getKanban(id);
    }

    onMounted(() => {
        const pipelineId = Number(route.query.pipeline_id);
        loadKanban(pipelineId || props.pipelines[0].id);
    });

    watch(
        () => route.query.pipeline_id,
        (value) => {
            if (value) loadKanban(Number(value));
    });

    async function onLeadDrop(leadId: number, fromStageId: number, toStageId: number) {
        moveLead(leadId, fromStageId, toStageId);
        try {
            await updateStageLead(leadId, toStageId);
        } catch(error) {
            console.error(error);
            moveLead(leadId, toStageId, fromStageId);
        }
    }

    function moveLead(leadId: number, fromStageId: number, toStageId: number) {
        const fromStage = kanban.value?.stages.find(stage => stage.id === fromStageId);
        const toStage = kanban.value?.stages.find(stage => stage.id === toStageId);

        if (!fromStage || !toStage) {
            return;
        }

        const index = fromStage.leads.findIndex(lead => lead.id === leadId);

        if (index === -1) {
            return;
        }

        const [lead] = fromStage.leads.splice(index, 1);
        fromStage.leadCount -= 1
        fromStage.moneyAmount -= lead.moneyAmount
        toStage.leadCount += 1
        toStage.moneyAmount += lead.moneyAmount
        toStage.leads.push(lead);
    }
</script>

<style>
    .pipelines a {
        margin-left: 15px;
    }
    .stages {
        display: flex;
    }
</style>