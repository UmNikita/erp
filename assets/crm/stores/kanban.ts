import { defineStore } from 'pinia';
import { ref } from 'vue';
import { Kanban } from '../types/kanban';
import { leadResponseToUi, mapKanban } from '../mappers/leadMapper';
import { getKanban } from '../api/kanban';
import { useLRUCache } from '../composables/useLRUCache';
import { useRequestDeduplication } from '../composables/useRequestDeduplication';
import { StageRequest } from '../types/stage';
import { createStage as createStageApi, deleteStage as deleteStageApi } from '../api/stage';
import { responseToStage } from '../mappers/stageMapper';
import { usePipelineStore } from './pipelines';
import { renameStage as renameStageApi } from '../api/stage.ts';
import { createStageLead } from '../api/lead.ts';
import { LeadRequest, LeadResponse, Status } from '../types/lead.ts';
import { deleteLead as deleteLeadApi, lostLead, successLead } from '../api/lead';
import { useClientTableStore } from './clientTable.ts';
import { useLeadTableStore } from './leadArchiveTable.ts';

export const useKanbanStore = defineStore('kanban', () => {

    const kanban = ref<Kanban | null>(null);
    const kanbanCurrentID = ref<number | null>(null);
    const kanbanCache = useLRUCache<Kanban>(5);
    const {canRequest, release} = useRequestDeduplication();
    const pipelineStore = usePipelineStore();
    const clientTableStore = useClientTableStore();
    const leadArchiveStore = useLeadTableStore();
    
    const loading = ref(false);
    const error = ref(false);

    async function loadKanban(id: number) {
        if (loading.value || kanbanCurrentID.value == id)
            return;

        kanbanCurrentID.value = id;
        loading.value = true;
        try {
            const cached = kanbanCache.get(id);
            if (cached) {
                kanban.value = cached;
                void refreshKanban(id);
            }
            else {
                await refreshKanban(id);
            }
        }
        catch (e) {
            error.value = true;
        }
        finally {
            loading.value = false
        }
    }

    function clear(): void {
        kanban.value = null;
        kanbanCurrentID.value = null;
        loading.value = false;
        error.value = false;

        kanbanCache.clear();
    }

    async function refreshKanban(id: number): Promise<Kanban | null> {
        if (!canRequest(id)) {
            return null;
        }
        try {
            const fresh = mapKanban(await getKanban(id));
            kanbanCache.set(id, fresh);
            if(id == kanbanCurrentID.value)
                kanban.value = fresh;
            return fresh;
        } finally {
            release(id);
        }
    }

    async function createStage(data: StageRequest): Promise<void> {
        const stage = await createStageApi(data);
        const stageUI = responseToStage(stage);
        kanban.value?.stages.push(stageUI);
        const pipeline = pipelineStore.pipelinesDetail.find(pipeline => pipeline.id === data.pipeline_id);
        if(pipeline != null)
        {
            pipeline.stages.push(stage);
        }
    }

    async function renameStage(stageId: number, newName: string): Promise<void> {
        await renameStageApi(newName, stageId);
        const pipeline = pipelineStore.pipelinesDetail.find(pipeline => pipeline.id === kanbanCurrentID.value);
        if(pipeline != null) {
            const stage = pipeline?.stages.find(stage => stage.id === stageId);
            if(stage != null)
            {
                stage.name = newName;
            }
        }
    }

    async function deleteStage(id: number): Promise<void> {
        await deleteStageApi(id);
        if(kanban.value) {
            kanban.value.stages = kanban.value.stages.filter(stage => stage.id !== id);
            const pipeline = pipelineStore.pipelinesDetail.find(pipeline => pipeline.id === kanbanCurrentID.value);
            if(pipeline != null)
                pipeline.stages = pipeline.stages.filter(stage => stage.id !== id);
        }
    }

    async function createLead(data: LeadRequest): Promise<void | LeadResponse> {
        const lead = await createStageLead(data);
        if(!kanban.value)
            return lead;
        const leadUi = leadResponseToUi(lead);
        const stage = kanban.value?.stages.find(stage => stage.id === data.stage_id);
        if(stage) {
            stage.leadCount += 1;
            stage.moneyAmount += lead.budget;
            stage.leads.push(leadUi);
        }
        kanban.value.leadsCount += 1;
        kanban.value.moneyAmount += lead.budget;
        clientTableStore.addClient(lead.client);
    }

    async function deleteLead(leadId: number, leadStageId: number, isDelete: boolean = true, status: Status = Status.won): Promise<boolean | null> {
        if(isDelete) {
            await deleteLeadApi(leadId);
        } else {
            if(status == Status.won) {
                await successLead(leadId);
            } else if (status == Status.lost) {
                await lostLead(leadId);
            }
        }
        const stage = kanban.value?.stages.find(stage => stage.id === leadStageId);
        if (!stage)
            return null;
        const lead = stage?.leads.find(lead => lead.id == leadId);
        leadArchiveStore.refresh();
        stage.leads = stage?.leads.filter(lead => lead.id !== leadId);
        stage.leadCount -= 1;
        if(kanban.value)
            kanban.value.leadsCount -= 1;
        if(lead) {
            stage.moneyAmount -= lead.moneyAmount;
            if(kanban.value)
                kanban.value.moneyAmount -= lead.moneyAmount;
        }
        return true;
    }

    return {
        kanban,
        error,
        loadKanban,
        createStage,
        renameStage,
        deleteStage,
        createLead,
        deleteLead,
        clear
    }
})