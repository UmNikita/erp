<template>
    <div class="section-head">
        <div>
            <h2>Контакты клиента</h2>
            <p>Все контакты «{{ client?.name }}»</p>
        </div>
        <button class="btn btn-primary" @click="openModalCreate">+ Создать контакт</button>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Имя</th>
                    <th>Фамилия</th>
                    <th>Отчество</th>
                    <th>Должность</th>
                    <th>Телефон</th>
                    <th>Email</th>
                    <th>Мессенджер</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <ContactRow @edit="openModalEdit" @delete="acceptDeleteContact" v-for="contact in client?.contacts" :contact="contact" />
            </tbody>
        </table>
    </div>
    <ContactModal v-if="activeModal === MODALS.CREATE_CONTACT" :error="generalError" :contact="contact"
        :isEdit="isEdit" :errors="errors" @close="closeModalContact" @submit="acceptAddContact" @edit="acceptEditContact"
    />
</template>

<script setup lang="ts">
    import { ref } from 'vue';
    import { ContactRequest } from '../../../../../types/contact.ts';
    import ContactModal from '../../../modals/ContactModal.vue';
    import ContactRow from './ContactRow.vue';
    import { MODALS, useModal } from '../../../../../composables/useModal.ts';
    import { useContactForm } from '../../../../../composables/Clients/useContactForm.ts';
    import { useCurrentClient } from '../../../../../composables/Clients/useCurrentClient.ts';
    import { deleteContact } from "../../../../../api/contacts.ts";

    const {activeModal, generalError, closeModal, openModal} = useModal();
    const {createContact, updateContact} = useContactForm();

    const errors = ref<Record<string, string>>({});
    const isEdit = ref(false);
    const contact = ref();

    const { client, newContact } = useCurrentClient();

    function openModalEdit(id: number) {
        const obj = client.value?.contacts.find(contact => contact.id === id);
        contact.value = obj;
        isEdit.value = true;
        openModal(MODALS.CREATE_CONTACT);
    }

    function openModalCreate() {
        isEdit.value = false;
        openModal(MODALS.CREATE_CONTACT);
    }

    async function acceptAddContact(data: ContactRequest) {
        data.client_id = client.value.id;
        const contact = await createContact(data, errors, generalError);
        if(contact == null) return;
        newContact(contact);
        closeModal();
    }

    async function acceptEditContact(data: ContactRequest, id: number) {
        const obj = client.value?.contacts.find(contact => contact.id === id);
        if(!obj)
            return;
        const contact = await updateContact(obj, data, errors, generalError);
        if(contact == null) return;
        Object.assign(obj, contact);
        closeModal();
    }

    async function acceptDeleteContact(id: number) {
        if (!confirm("Вы действительно хотите удалить контакт"))
            return;
        try {
            await deleteContact(id);
        }
        catch {
            alert("Возникла ошибка");
            return;
        }
        if(client.value)
            client.value.contacts = client.value.contacts.filter(value => value.id !== id);
    }

    function closeModalContact() {
        errors.value = {};
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