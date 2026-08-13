<template>
  <CreateModalWrapper title-btn="Отправить" title="Отправить КП" subtitle="Заполните информацию для письма"
  :error="error" @submit="submit" @close="close">
    <TextField :required="true" :ico="ClientIco" title="Имя менеджера" v-model="manager_name" 
    placeholder="Введите имя менеджера" :error="errors.manager_name" />
    <TextField :required="true" :ico="ClientIco" title="Телефон менеджера" v-model="manager_phone" 
    placeholder="Введите телефон менеджера" :error="errors.manager_phone" />
    <SelectField :required="false" :ico="ClientIco" title="Контакт (если не выбрать, КП отправится на почту клиента)" v-model="contact" 
    :elements="contacts" firstElement="Выбирите контакт" :error="errors.contact" />
  </CreateModalWrapper>
</template>

<script setup lang="ts">
  import { ref } from 'vue'
  import ClientIco from '../../icons/Kanban/ClientIco.vue';
  import TextField from '../../CRM/modals/fields/TextField.vue';
  import SelectField from '../../CRM/modals/fields/SelectField.vue';
  import CreateModalWrapper from '../../CRM/modals/CreateModalWrapper.vue'
  import { Contact } from '../../../types/contact.ts';

  const emit = defineEmits(['close', 'submit'])

  const manager_name = ref('');
  const manager_phone = ref('');
  const contact = ref();
  const props = defineProps<{ 
    error?: string | null,
    contacts: Contact[],
    errors: Record<string, string>
  }>();

  function close() {
    emit('close')
  }

  function submit() {
    const contact_id = contact.value;
    const currentContact = props.contacts.find(contact => contact.id === contact_id);
    const data = {
      manager_name: manager_name.value,
      manager_phone: manager_phone.value,
      contact_id: contact_id,
      contact_email: currentContact?.email
    };
    emit('submit', data);
  }
</script>
<style>
.create-modal__btn {
  display: flex;
  justify-content: center;
}
.create-modal__btn button {
    width: 45%;
    height: 42px;

    border: 1px dashed #cfd6df;
    border-radius: 8px;

    background: #f8fafc;
    color: #374151;

    font-size: 14px;
    font-weight: 500;

    cursor: pointer;

    transition: all 0.15s ease;
}

.create-modal__btn button:hover {
    background: #f1f5f9;
    border-color: #9ca3af;
}

.create-modal__btn button:active {
    transform: scale(0.98);
}
</style>