<template>
    <section>
        <div class="section-head">
            <div>
                <h2>Письма клиенту</h2>
                <p>Отправленные письма, черновики и ошибки доставки</p>
            </div>
            <div>
                <button class="btn btn-primary open-mail">+ Написать письмо</button>
                <button class="btn btn-primary open-mail" @click="openModal(MODALS.SEND_KP)">Отправить КП</button>
            </div>
        </div>
        <div class="mail-filters">
            <button class="mail-filter active">Все</button>
            <button class="mail-filter">Отправленные</button>
            <button class="mail-filter">Черновики</button>
            <button class="mail-filter">Ошибки</button>
        </div>
        <div class="mail-list">
            <Email v-for="email in historyEmails" :email="email" />
        </div>
    </section>
    <KpModal v-if="activeModal === MODALS.SEND_KP" :error="generalError" :errors="errors" 
    :contacts="client?.contacts" @close="closeModalKp" @submit="acceptSendKp"
    />
</template>

<script setup lang="ts">
    import { ref } from 'vue';
    import { LeadRequest } from '../../../../../types/lead';
    import KpModal from '../../../modals/KpModal.vue';
    import { MODALS, useModal } from '../../../../../composables/useModal.ts';
    import { useCurrentClient } from '../../../../../composables/Clients/useCurrentClient.ts';
    import { useEmailForm } from '../../../../../composables/Clients/useEmailForm.ts';
    import Email from './Email.vue';

    const {activeModal, generalError, closeModal, openModal} = useModal();
    const { sendKP } = useEmailForm();
    const errors = ref<Record<string, string>>({});

    const { client, historyEmails  } = useCurrentClient();

    function closeModalKp() {
        errors.value = {};
        closeModal();
    }

    async function acceptSendKp(data: LeadRequest) {
        const res = await sendKP(client.value, data, errors, generalError);
        if(res == null)
            return;
        closeModal();
    }

</script>

<style scoped>

    .btn-primary {
        background: #1267f4;
        color: #fff;
        box-shadow: 0 6px 14px rgba(18, 103, 244, .18);
        margin-left: 15px;
    }
    
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

    .mail-filters {
        display: flex;
        gap: 7px;
        margin-bottom: 18px;
        overflow: auto;
    }

    .mail-filter {
        height: 36px;
        padding: 0 13px;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #fff;
        color: #606b7e;
        font-size: 12px;
        font-weight: 600;
    }

    .mail-filter.active {
        color: var(--blue);
        background: var(--soft);
        border-color: #cddfff;
    }

    .mail-list {
        display: grid;
        gap: 10px;
    }
</style>