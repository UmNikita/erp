<template>
  <CreateModalWrapper v-if="client" :accepting="accepting" title-btn="Отправить" title="Отправить КП" subtitle="Заполните информацию для письма"
  :error="generalError" @submit="submit" @close="emit('close')">
    <TextField :required="true" :ico="ClientIco" title="Имя менеджера" v-model="manager_name" 
    placeholder="Введите имя менеджера" :error="errors.manager_name" />
    <TextField :required="true" :ico="ClientIco" title="Телефон менеджера" v-model="manager_phone" 
    placeholder="Введите телефон менеджера" :error="errors.manager_phone" />
    <SelectField :required="false" :ico="ClientIco" title="Контакт (если не выбрать, КП отправится на почту клиента)" v-model="contact" 
    :elements="client.contacts" firstElement="Выбирите контакт" :error="errors.contact_id" />
  </CreateModalWrapper>
</template>

<script setup lang="ts">
  import { ref } from 'vue'
  import ClientIco from '../../icons/Kanban/ClientIco.vue';
  import TextField from '../../CRM/modals/fields/TextField.vue';
  import SelectField from '../../CRM/modals/fields/SelectField.vue';
  import CreateModalWrapper from '../../CRM/modals/CreateModalWrapper.vue'
  import { validateKP } from '../../../validators/client.ts';
  import { useCurrentClient } from '../../../composables/Clients/useCurrentClient.ts';
  import { KPErrors } from '../../../types/client.ts';
  import { sendKP } from '../../../api/client.ts';

  const emit = defineEmits(['close']);
  const { client } = useCurrentClient();

  const manager_name = ref('');
  const manager_phone = ref('');
  const contact = ref();

  const accepting = ref(false);
  const generalError = ref<string | null>(null);
  const errors = ref<KPErrors>({
    manager_name: null,
    manager_phone: null,
    contact_id: null,
    contact_email: null
  });

  async function submit() {
    if(!client.value)
      return;
    
    const contact_id = contact.value;
    const currentContact = client.value.contacts.find(contact => contact.id === contact_id);
    const data = {
      manager_name: manager_name.value,
      manager_phone: manager_phone.value,
      contact_id: contact_id,
      contact_email: currentContact?.email
    };
    const validationErrors = validateKP(data);
    if (!validationErrors.isValid) {
      errors.value = validationErrors.errors;
      return false;
    }
    accepting.value = true;

    if(!client.value.email && !data.contact_id) {
      generalError.value = "Выберете контакт или добавьте клиенту почту!";
      accepting.value = false;
      return null;
    }

    if(data.contact_id && !data.contact_email) {
      generalError.value = "У выбранного контакта нет почты!";
      accepting.value = false;
      return null;
    }
        
    try {
      console.log(data)
      await sendKP(client.value.id, data);
      return true;
    }
    catch {
      generalError.value = 'Не удалось отправить письмо. Попробуйте чуть позже';
      return null;
    }
    finally {
      accepting.value = false;
    }
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