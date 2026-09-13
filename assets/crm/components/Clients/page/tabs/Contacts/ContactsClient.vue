<template>
    <div class="section-head">
        <div>
            <h2>Контакты клиента</h2>
            <p>Все контакты «{{ client?.name }}»</p>
        </div>
        <button class="btn btn-primary" @click="openModalCreate">+ Создать контакт</button>
    </div>
    <ContactTable v-if="client" :contacts="client.contacts" @edit="openModalEdit" />
    <ContactModal v-if="activeModal === MODALS.CREATE_CONTACT" :contact="contactEdit"
        :isEdit="isEdit" @close="closeModal"
    />
</template>

<script setup lang="ts">
    import { ref } from 'vue';
    import { Contact, ContactRequest } from '../../../../../types/contact.ts';
    import ContactModal from '../../../modals/ContactModal.vue';
    import { MODALS, useModal } from '../../../../../composables/useModal.ts';
    import { useCurrentClient } from '../../../../../composables/Clients/useCurrentClient.ts';
    import ContactTable from './ContactTable.vue';

    const {activeModal, closeModal, openModal} = useModal();
    const errors = ref<Record<string, string>>({});
    const isEdit = ref(false);
    const contactEdit = ref<undefined | Contact>();

    const { client } = useCurrentClient();

    function openModalEdit(id: number) {
        const obj = client.value?.contacts.find(contact => contact.id === id);
        contactEdit.value = obj;
        isEdit.value = true;
        openModal(MODALS.CREATE_CONTACT);
    }

    function openModalCreate() {
        isEdit.value = false;
        openModal(MODALS.CREATE_CONTACT);
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

    .table-wrap {
        overflow: auto;
        border: 1px solid var(--line);
        border-radius: 12px;
        min-height: 800px;
    }

    .table {
        width: 100%;
        min-width: 1120px;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .table th {
        height: 50px;
        padding: 0 16px;
        background: #f8f9fb;
        border-bottom: 1px solid var(--line);
        color: var(--muted);
        font-size: 12px;
        text-align: left;
        text-transform: uppercase;
    }
</style>