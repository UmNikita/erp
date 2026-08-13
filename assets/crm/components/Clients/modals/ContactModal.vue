<template>
  <CreateModalWrapper :title-btn="isEdit ? 'Обновить' : 'Создать'" title="Создание клиентп" subtitle="Заполните информацию о клиенте"
  :error="error" @submit="submit" @close="emit('close')">
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
  import { Contact } from '../../../types/contact.ts';

  const emit = defineEmits(['close', 'submit', 'edit']);

  const name = ref('');
  const secondname = ref('');
  const thirdname = ref('');
  const position = ref('');
  const phone = ref('');
  const email = ref('');
  const messenger = ref('');

  function setValues() {
    name.value = props.contact.name;
    secondname.value = props.contact.secondname;
    thirdname.value = props.contact.thirdname;
    position.value = props.contact.position;
    phone.value = props.contact.phone;
    email.value = props.contact.email;
    messenger.value = props.contact.messenger;
  }

  function submit() {
    const data = {
      name: name.value,
      secondname: secondname.value,
      thirdname: thirdname.value,
      position: position.value,
      phone: phone.value,
      email: email.value,
      messenger: messenger.value,
    }
    if(props.isEdit == true)
      emit("edit", data, props.contact.id);
    else
      emit("submit", data);
  }

  const props = defineProps<{
    error?: string | null,
    errors: Record<string, string>,
    isEdit: boolean,
    contact: Contact
  }>();

  onMounted(()=>{
    if(props.isEdit)
      setValues();
  });
</script>