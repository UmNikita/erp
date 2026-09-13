<template>
  <section class="deal-information">
    <h2 class="section-title">Информационное поле</h2>
    <div class="info-grid">
      <Lead :lead="lead" />
      <template v-if="lead.client">
        <Client :client="lead.client" />
        <!-- <Contact :contact="contact" v-for="contact in lead.client.contacts" /> -->
      </template>
      <div class="info-card info-card--empty" v-if="!lead.client">
        <button class="add-card-button" @click="addBtn">
          <PlusIco />
        </button>
      </div>
    </div>
  </section>
  <ClientModal :is-submit="true" v-if="activeModal === MODALS.CREATE_CLIENT"  
    @submit="acceptAddClient" @close="closeModal" />
  <!-- <ContactModal 
    v-if="activeModal === MODALS.CREATE_CONTACT" :error="generalError" :errors="errors"
    @close="closeModalClient" @submit="acceptAddContact" :is-edit="false" :contact="null"
  /> -->
</template>

<script setup lang="ts">
  import { MODALS, useModal } from '../../../composables/useModal.ts';
  import { LeadDetail } from '../../../types/lead.ts';
  import ClientModal from '../../Clients/modals/ClientModal.vue';
  import Client from './Client.vue';
  import Lead from './Lead.vue';
  import PlusIco from '../../icons/PlusIco.vue';
  import { ClientRequest } from '../../../types/client.ts';
  import { setClientLead } from '../../../api/lead.ts';
  import { newClientToDetail } from '../../../mappers/clientMapper.ts';
  import { createClient } from '../../../api/client.ts';

  const {activeModal, closeModal, openModal} = useModal();
  const props = defineProps<{
    lead: LeadDetail;
  }>();

  // async function acceptAddContact(data: ContactRequest) {
  //   data.client_id = props.lead.client.id;
  //   //const contact = await createContact(data, errors, generalError);
  //   if(contact == null) return;
  //   if(props.lead.client?.contacts)
  //     props.lead.client?.contacts.push(contact);
  //   else
  //     props.lead.client.contacts = [contact];
  //   closeModal();
  // }

  async function acceptAddClient(data: ClientRequest) {
    const client = await createClient(data);
    if(client) {
      await setClientLead(props.lead.id, client.id);
      props.lead.client = newClientToDetail(client, props.lead);
      closeModal();
    }
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