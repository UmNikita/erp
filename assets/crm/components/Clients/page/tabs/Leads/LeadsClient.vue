<template>
    <section>
        <div class="section-head">
            <div>
                <h2>Сделки клиента</h2>
                <p>Все сделки, связанные с «{{ client?.name }}»</p>
            </div>
            <button class="btn btn-primary" @click="openModal(MODALS.CREATE_LEAD)">+ Создать сделку</button>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Воронка</th>
                        <th>Этап</th>
                        <th>Сумма</th>
                        <th>Статус</th>
                        <th>Ответственный</th>
                        <th>Создана</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <LeadRow v-for="lead in leads" :lead="lead" />
                </tbody>
            </table>
        </div>
    </section>
    <LeadModal v-if="activeModal === MODALS.CREATE_LEAD" :error="generalError" :errors="errors" 
    :pipelines-detail="pipelines" :responsibles="responsibles" @close="closeModalLead" @submit="acceptAddLead"
    />
</template>

<script setup lang="ts">
    import { ref } from 'vue';
    import { LeadRequest } from '../../../../../types/lead';
    import LeadModal from '../../../modals/LeadModal.vue';
    import LeadRow from './LeadRow.vue';
    import { MODALS, useModal } from '../../../../../composables/useModal.ts';
    import { useLeadForm } from '../../../../../composables/CRM/leads/useLeadForm.ts';
    import { useCurrentClient } from '../../../../../composables/Clients/useCurrentClient.ts';

    const {activeModal, generalError, closeModal, openModal} = useModal();
    const {createLead} = useLeadForm();
    const errors = ref<Record<string, string>>({});

    const {leads, client, pipelines, responsibles, newLead } = useCurrentClient();

    function closeModalLead() {
        errors.value = {};
        closeModal();
    }

    async function acceptAddLead(data: LeadRequest) {
        data.client_id = client.value?.id;
        const lead = await createLead(data, false, errors, generalError);
        if(lead == null) return;
        newLead(lead);
        closeModal();
    }

</script>

<style scoped>

    .section-head {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .section-head h2 {
        margin: 0 0 5px;
        font-size: 20px;
    }

    .section-head p {
        margin: 0;
        color: var(--muted);
        font-size: 13px;
    }

    .btn-primary {
        background: #1267f4;
        color: #fff;
        box-shadow: 0 6px 14px rgba(18, 103, 244, .18);
    }

    .table-wrap {
        overflow: auto;
        border-radius: 12px;
    }

    .table {
        width: 100%;
        min-width: 880px;
        border-collapse: collapse;
    }

    .table th {
        height: 48px;
        padding: 0 15px;
        background: #f8f9fb;
        border-bottom: 1px solid var(--line);
        color: var(--muted);
        font-size: 12px;
        text-align: left;
        text-transform: uppercase;
    }
</style>