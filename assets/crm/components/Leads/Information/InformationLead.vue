<template>
  <section class="deal-information">
    <h2 class="section-title">Информационное поле</h2>
    <div class="info-grid">
      <Lead :lead="lead" />
      <template v-if="lead.client">
        <Client :client="lead.client" />
        <Contact :contact="contact" v-for="contact in lead.client.contacts" />
      </template>
      <div class="info-card info-card--empty">
        <button class="add-card-button" @click="addBtn">
          <svg viewBox="0 0 24 24" fill="none"><path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
      </div>
    </div>
  </section>
  <ClientModal 
    v-if="activeModal === MODALS.CREATE_CLIENT" :error="generalError" :errors="errors"
    @close="closeModalClient" @submit="acceptAddClient"
  />
  <ContactModal 
    v-if="activeModal === MODALS.CREATE_CONTACT" :error="generalError" :errors="errors"
    @close="closeModalClient" @submit="acceptAddContact" :is-edit="false" :contact="null"
  />
</template>

<script setup lang="ts">
  import { ref } from 'vue';
  import { MODALS, useModal } from '../../../composables/useModal.ts';
  import { LeadDetail } from '../../../types/lead.ts';
  import ClientModal from '../../Clients/modals/ClientModal.vue';
  import ContactModal from '../../Clients/modals/ContactModal.vue';
  import Client from './Client.vue';
  import Contact from './Contact.vue';
  import Lead from './Lead.vue';
  import { ClientRequest } from '../../../types/client.ts';
  import { setClientLead } from '../../../api/lead.ts';
  import { useClientForm } from '../../../composables/Clients/useClientForm.ts';
  import { newClientToDetail } from '../../../mappers/clientMapper.ts';
  import { ContactRequest } from '../../../types/contact.ts';
  import { useContactForm } from '../../../composables/Clients/useContactForm.ts';

  const {activeModal, generalError, closeModal, openModal} = useModal();
  const errors = ref<Record<string, string>>({});
  const {createClient} = useClientForm();
  const {createContact} = useContactForm();
  const props = defineProps<{
    lead: LeadDetail;
  }>();

  async function acceptAddClient(data: ClientRequest) {
    const client = await createClient(data, errors, generalError);
    if(client) {
      await setClientLead(props.lead.id, client.id);
      props.lead.client = newClientToDetail(client, props.lead);
      closeModal();
    }
  }

  async function acceptAddContact(data: ContactRequest) {
    data.client_id = props.lead.client.id;
    const contact = await createContact(data, errors, generalError);
    if(contact == null) return;
    if(props.lead.client?.contacts)
      props.lead.client?.contacts.push(contact);
    else
      props.lead.client.contacts = [contact];
    closeModal();
  }

  function closeModalClient() {
    errors.value = {};
    closeModal();
  }

  function addBtn() {
    if(props.lead.client) {
      openModal(MODALS.CREATE_CONTACT);
    } else {
      openModal(MODALS.CREATE_CLIENT);
    }
  }

</script>

<style scoped> 
  .deal-information {
      background: #fff;
      border: 1px solid #e4e8ee;
      border-radius: 13px;
      box-shadow: 0 2px 8px rgba(28,39,56,.025);
  }

  .deal-information {
      padding: 18px;
  }

  .section-title {
    margin: 0 0 18px;
    font-size: 16px;
    font-weight: 650;
  }

  .info-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
  }

  .info-card {
    min-height: 255px;
    padding: 15px;
    background: #fff;
    border: 1px solid #dde2e9;
    border-radius: 10px;
  }

  .info-card--empty {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .add-card-button {
    width: 58px;
    height: 58px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1267f4;
    background: #fff;
    border: 1px solid #dce3eb;
    border-radius: 50%;
    box-shadow: 0 3px 10px rgba(25,47,80,.05);
    cursor: pointer;
  }

  .add-card-button:hover {
    background: #f5f9ff;
    border-color: #bfd5ff;
  }

  .add-card-button svg {
    width: 28px;
    height: 28px;
  }

  @media (max-width: 900px) {
      .info-grid {
          grid-template-columns: repeat(2, minmax(0, 1fr));
      }
  }

  @media (max-width: 700px) {
    .info-grid {
      grid-template-columns: 1fr;
    }

    .info-card {
      min-height: 0;
    }

    .info-row {
      grid-template-columns: 105px minmax(0, 1fr);
    }
  }
</style>