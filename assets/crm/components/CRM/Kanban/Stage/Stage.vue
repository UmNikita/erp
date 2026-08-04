<template>
    <section class="column" :style="{ '--accent': stage.color }" @dragover.prevent @drop="dropLead">
        <header class="column-header">
            <div class="column-header-title">
                <h2 v-if="!editingName" class="column-title">{{stage.name}}</h2>
                <input
                    v-else
                    ref="nameInput"
                    v-model="name"
                    class="column-inp"
                    @keyup.enter="saveName(props, emit)"
                    @keyup.esc="cancelEdit(props)"
                    @blur="saveName(props, emit)"
                />
                <StageMenu @rename="startEditName(props)" @delete="deleteStage()"
                @forward="forward" @back="back" />
            </div>
            <div class="column-meta">
                <span>{{stage.leadCount}} {{getPluralizeLead(stage.leadCount)}}</span>
                <span>{{stage.moneyAmount}} ₽</span>
            </div>
        </header>
        <div class="column-body" v-if="stage?.leads.length">
            <Lead v-for="lead in stage?.leads" :key="lead.id" :lead="lead" :stage-id="stage.id" />
        </div>
        <EmptyLeads v-else />
    </section>
</template>

<script setup lang="ts">
    import { StageUI } from '../../../../types/stage.ts';
    import { getPluralizeLead } from '../../../../utils/words.ts';
    import EmptyLeads from '../Empty/EmptyLeads.vue';
    import Lead from '../Lead/Lead.vue';
    import StageMenu from './StageMenu.vue';
    import { useRenameStage } from '../../../../composables/CRM/stages/useRenameStage.ts';

    const props = defineProps<{
        stage: StageUI,
        pipelineId: number,
    }>();

    const {editingName, name, nameInput, startEditName, cancelEdit, saveName} = useRenameStage(props.stage.name, props.pipelineId)

    const emit = defineEmits<{
        leadDrop: [
            leadId: number,
            fromStageId: number,
            toStageId: number
        ],
        delete: [id: number],
        forward: [stage: StageUI],
        back: [stage: StageUI]
    }>();

    async function deleteStage() {
        if(!confirm(`Вы действительно хотите удалить этап "${props.stage.name}"`))
            return;
        if(props.stage.leadCount > 0) {
            alert("Нельзя удалить этап с лидами");
            return;
        }
        emit("delete", props.stage.id);
    }

    async function dropLead(event: DragEvent) {
        const leadId = Number(event.dataTransfer?.getData('leadId'));
        const leadStageId = Number(event.dataTransfer?.getData('stageId'));
        const toStage = props.stage.id;

        if(leadStageId != toStage) {
            emit('leadDrop', leadId, leadStageId, toStage);
        }   
    }

    async function forward() {
        emit('forward', props.stage);
    }

    function back() {
        emit('back', props.stage);
    }
</script>

<style>
    .column {
      --accent: var(--blue);
      position: relative;
      flex-direction: column;
      border: 1px solid var(--line);
    }

    .column::before {
      content: "";
      position: absolute;
      right: -1px;
      left: -1px;
      height: 5px;
      background: var(--accent);
    }

    .column-header {
      padding: 26px 16px 20px;
    }

    .column-header-title {
        display: flex;
        justify-content: space-between;
    }

    .column-title {
        margin: 0 0 8px;
        font-size: 18px;
        max-width: 240px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .column-inp {
      margin: 0 0 8px;
      font-size: 18px;
      outline: none;
      border: none;
      border-bottom: 1px solid gray;  
    }

    .column-meta {
      display: flex;
      justify-content: space-between;
      font-size: 14px;
    }

    .column-body {
      flex: 1;
      padding: 0 4px 12px;
      overflow-y: auto;
      scrollbar-width: thin;
      scrollbar-color: #cfd5df transparent;
    }
</style>