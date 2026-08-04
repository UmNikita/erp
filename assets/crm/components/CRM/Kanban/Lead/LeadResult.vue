<template>
    <div class="board-lead-result">
        <div class="result-btn delete" @dragover.prevent @drop="dropLostLead">
            <span>Провал</span>
        </div>

        <div class="result-btn success" @dragover.prevent @drop="dropWonLead">
            <span>Успех</span>
        </div>

        <div class="result-btn fail" @dragover.prevent @drop="dropDeleteLead">
            <span>Удалить</span>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { deleteLead as deleteLeadApi, lostLead, successLead } from '../../../../api/lead';
    import { usePipelineKanban } from '../../../../composables/CRM/pipelines/usePipelineKanban';

    const { deleteLead } = usePipelineKanban();

    async function dropLostLead(event: DragEvent) {
        const leadId = Number(event.dataTransfer?.getData('leadId'));
        const leadStageId = Number(event.dataTransfer?.getData('stageId'));
        try {
            await lostLead(leadId);
            deleteLead(leadId, leadStageId);
        }
        catch {
            alert("Возникла ошибка! Попробуйте позже");
        }
    }

    async function dropWonLead(event: DragEvent) {
        const leadId = Number(event.dataTransfer?.getData('leadId'));
        const leadStageId = Number(event.dataTransfer?.getData('stageId'));
        try {
            await successLead(leadId);
            deleteLead(leadId, leadStageId);
        }
        catch {
            alert("Возникла ошибка! Попробуйте позже");
        }
    }

    async function dropDeleteLead(event: DragEvent) {
        const leadId = Number(event.dataTransfer?.getData('leadId'));
        const leadStageId = Number(event.dataTransfer?.getData('stageId'));
        try {
            await deleteLeadApi(leadId);
            deleteLead(leadId, leadStageId);
        }
        catch {
            alert("Возникла ошибка! Попробуйте позже");
        }
    }
</script>

<style>
    .board-lead-result {
        height: 64px;
        width: 70%;
        margin: 48px auto 0;

        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;

        border: 1px dashed var(--line);
        border-radius: 16px;
        background: #fafbfc;
    }


    .result-btn {
        height: 42px;
        padding: 0 18px;
        width: 25%;
        display: flex;
        align-items: center;
        gap: 8px;
        justify-content: center;

        border-radius: 12px;
        border: 1px solid var(--line);
        background: white;

        font-size: 14px;
        font-weight: 500;

        transition: .18s ease;
    }


    .result-btn svg {
        width: 18px;
        height: 18px;
    }

    .result-btn.delete {
        background: #fff1f1;
        border-color: #ffb4b4;
        color: #d93025;
    }


    .result-btn.success {
        background: #effcf3;
        border-color: #9ee2b1;
        color: #16803c;
    }


    .result-btn.fail {
        background: #f5f5f5;
        border-color: #cfd3d8;
        color: #555;
    }
</style>