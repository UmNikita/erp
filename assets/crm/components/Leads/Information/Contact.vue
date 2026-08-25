<template>
  <div class="info-card">
    <div class="info-card__header">
      <span class="info-card__title">Контактное лицо</span>
      <button v-if="!editing" @click="startEditing" class="info-card__edit">
          <svg viewBox="0 0 24 24" fill="none"><path d="M14 5L19 10M4 20L7.5 19.3L19 7.8A2.1 2.1 0 0 0 16.2 5L4.7 16.5L4 20Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
      </button>
      <div v-else>
          <button class="link accept" @click="acceptEditing">Принять</button>
          <button class="link" @click="cancelEditing">Отменить</button>
      </div>
    </div>

    <RenameField title-field="Фамилия" :value-field="contact.secondname" 
      class="info-row" :editing="editing" :error="errors.secondname" v-model="data.secondname" />
    
    <RenameField title-field="Имя" :value-field="contact.name" 
      class="info-row" :editing="editing" :error="errors.name" v-model="data.name" />

    <RenameField title-field="Отчество" :value-field="contact.thirdname" 
      class="info-row" :editing="editing" :error="errors.thirdname" v-model="data.thirdname" />

    <RenameField title-field="Должность" :value-field="contact.position" 
      class="info-row" :editing="editing" :error="errors.position" v-model="data.position" />

    <RenameField title-field="Телефон" :value-field="contact.phone" 
      class="info-row" :editing="editing" :error="errors.phone" v-model="data.phone" />

    <RenameField title-field="Email" :value-field="contact.email" 
      class="info-row" :editing="editing" :error="errors.email" v-model="data.email" />

    <RenameField title-field="Мессенджер" :value-field="contact.messenger" 
      class="info-row" :editing="editing" :error="errors.messenger" v-model="data.messenger" />

    <RenameField title-field="Примечание" :value-field="contact.note" 
      class="info-row" :editing="editing" :error="errors.note" v-model="data.note" />

    <p class="general-err" v-if="generalError">{{generalError}}</p>
  </div>
</template>

<script setup lang="ts">
  import { useInlineRenameForm } from '../../../composables/useInlineRenameForm.ts';
  import { Contact } from '../../../types/contact.ts';
  import { formatPhone } from '../../../utils/fields.ts';
  import RenameField from '../../common/RenameField.vue';
  import {useContactForm} from '../../../composables/Clients/useContactForm.ts';

  const props = defineProps<{
    contact: Contact;
  }>();

  const {updateContact} = useContactForm();

  const {editing, errors, data, generalError, 
    startEditing, cancelEditing, accept, setNewData} = useInlineRenameForm(props.contact);

  async function acceptEditing() {
    const newData = accept();
    if(newData == null)
      return;
    if(!props.contact)
      return;
    const res = await updateContact(props.contact, data.value, errors, generalError);
    if(res) {
      editing.value = false;
      setNewData();
    }
  }

</script>

<style scoped>
  .link {
    padding: 0;
    border: 0;
    background: transparent;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
  }

  .accept {
    margin-right: 15px;
  }
  .general-err {
    margin: 6px 0 0;
    padding: 4px 6px 4px 16px;

    color: #c9363e;
    background: #fff5f5;
    border: 1px solid #f2c9cc;
    border-radius: 8px;

    font-size: 10px;
    font-weight: 500;
  }
  .info-card {
    min-height: 255px;
    padding: 15px;
    background: #fff;
    border: 1px solid #dde2e9;
    border-radius: 10px;
  }

  .info-card__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 14px;
  }

  .info-card__title {
    color: #214982;
    font-size: 12px;
    font-weight: 700;
  }

  .info-card__edit {
    cursor: pointer;
    width: 25px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    color: #46617f;
    background: transparent;
    border: 0;
    border-radius: 6px;
  }

  .info-card__edit:hover {
    background: #f1f5fa;
  }

  .info-card__edit svg {
    width: 15px;
    height: 15px;
  }

  .info-row {
    display: grid;
    grid-template-columns: 122px minmax(0, 1fr);
    gap: 8px;
    margin-bottom: 10px;
    font-size: 10.5px;
    line-height: 1.45;
  }

  .info-row:last-child {
    margin-bottom: 0;
  }

  :deep(.info-row input) {
    border: none;
    outline: none;
    border-bottom: 1px solid #303744;
    width: 200px;
  }

  :deep(.err) {
    color: #d93d42;
    line-height: 1.4;
    font-weight: 500;
  }

  .info-row__value {
    min-width: 0;
    color: #263246;
    font-weight: 500;
    word-break: break-word;
  }

  .info-row__value--link {
    color: #1267f4;
  }

  @media (max-width: 700px) {
    .info-card {
      min-height: 0;
    }

    .info-row {
      grid-template-columns: 105px minmax(0, 1fr);
    }
  }
</style>