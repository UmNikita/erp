<template>
  <CreateModalWrapper :accepting="accepting" :title-btn="isEdit ? 'Обновить' : 'Создать'" title="Создание клиентп" subtitle="Заполните информацию о клиенте"
  :error="generalError" @submit="submit" @close="emit('close')">
    <TextField :required="true" :ico="ClientIco" title="Имя контакта"
    placeholder="Введите имя контакта" :error="errors.name" v-model="name" />
    <TextField :required="true" :ico="ClientIco" title="Фамилия контакта"
    placeholder="Введите фамилию контакта" :error="errors.secondname" v-model="secondname" />
    <TextField :required="false" :ico="ClientIco" title="Отчество контакта"
    placeholder="Введите отчество контакта" :error="errors.thirdname" v-model="thirdname" />
    <TextField :required="false" :ico="ClientIco" title="Должность контакта"
    placeholder="Введите должность контакта" :error="errors.position" v-model="position" />
    <TextField :required="false" :ico="ClientIco" title="Телефон контакта"
    placeholder="Введите телефон контакта" :error="errors.phone" v-model="phone" />
    <TextField :required="false" :ico="ClientIco" title="Email контакта"
    placeholder="Введите email контакта" :error="errors.email" v-model="email" />
    <TextField :required="false" :ico="ClientIco" title="Мессенджер контакта"
    placeholder="Введите мессенджер контакта" :error="errors.messenger" v-model="messenger" />
  </CreateModalWrapper>
</template>

<script setup lang="ts">
  import ClientIco from '../../icons/Kanban/ClientIco.vue';
  import TextField from '../../CRM/modals/fields/TextField.vue';
  import CreateModalWrapper from '../../CRM/modals/CreateModalWrapper.vue';
  import { onMounted, ref } from 'vue';
  import { Contact, ContactErrors, ContactRequest } from '../../../types/contact.ts';
  import { createContact, updateContact } from '../../../api/contacts.ts';
  import { validateContact } from '../../../validators/contact.ts';
  import { useCurrentClient } from '../../../composables/Clients/useCurrentClient.ts';

  const emit = defineEmits(['close']);
  const { client } = useCurrentClient();

  const accepting = ref(false);
  const generalError = ref<string | null>(null);
  const errors = ref<ContactErrors>({
    name: null,
    secondname: null,
    thirdname: null,
    position: null,
    phone: null,
    email: null,
    messenger: null,
    client_id: null,
  });

  const name = ref('');
  const secondname = ref('');
  const thirdname = ref('');
  const position = ref('');
  const phone = ref('');
  const email = ref('');
  const messenger = ref('');

  function setValues() {
    if(props.contact == null)
      return;
    name.value = props.contact.name;
    secondname.value = props.contact.secondname;
    thirdname.value = props.contact.thirdname;
    position.value = props.contact.position;
    phone.value = props.contact.phone;
    email.value = props.contact.email;
    messenger.value = props.contact.messenger;
  }

  function submit() {
    if(!client.value)
      return;
    const data: ContactRequest = {
      name: name.value,
      secondname: secondname.value,
      thirdname: thirdname.value,
      position: position.value,
      phone: phone.value,
      email: email.value,
      messenger: messenger.value,
      client_id: client.value.id
    }
    if(props.isEdit == true)
      edit(data);
    else
      create(data);
  }

  async function edit(data: ContactRequest) {
    const contact = client.value?.contacts.find(
      contact => contact.id == props.contact?.id
    );

    if (!contact || !props.contact)
      return;

    const validationErrors = validateContact(data);

    if (!validationErrors.isValid) {
      errors.value = validationErrors.errors;
      return false;
    }

    accepting.value = true;

    try {
      const changes: Partial<ContactRequest> = {};

      for (const key of Object.keys(data) as (keyof ContactRequest)[]) {
        if (data[key] !== props.contact[key]) {
          changes[key] = data[key];
        }
      }

      if (Object.keys(changes).length === 0) {
        emit('close');
        return null;
      }

      const updatedContact = await updateContact(changes, props.contact.id);

      if (updatedContact == null)
        return;

      Object.assign(contact, updatedContact);
    }
    catch {
      generalError.value = 'Не удалось обновить контакт. Попробуйте чуть позже';
      return null;
    }
    finally {
      accepting.value = false;
    }

    emit('close');
  }

  async function create(data: ContactRequest) {
    const validationErrors = validateContact(data);
    if (!validationErrors.isValid) {
      errors.value = validationErrors.errors;
      return false;
    }
    accepting.value = true;
    try {
      const contact = await createContact(data);
      if(contact == null) return;
      client.value?.contacts.push(contact);
    }
    catch {
      generalError.value = 'Не удалось создать контакт. Попробуйте чуть позже';
      return null;
    }
    finally {
      accepting.value = false;
    }
    emit('close');
  }

  const props = defineProps<{
    isEdit: boolean,
    contact: Contact | undefined
  }>();

  onMounted(()=>{
    if(props.isEdit)
      setValues();
  });
</script>