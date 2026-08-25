<template>
  <CreateModalWrapper title-btn="Создать" title="Создание клиента" subtitle="Заполните информацию о клиенте"
  :error="error" @submit="submit" @close="emit('close')">
    <TextField :required="true" :ico="ClientIco" title="Имя клиента"
    placeholder="Введите имя клиента" :error="errors.name" v-model="name" />
    <TextField :required="false" :ico="ClientIco" title="ИНН клиента"
    placeholder="Введите ИНН клиента" :error="errors.inn" v-model="inn" />
    <TextField :required="false" :ico="ClientIco" title="Сфера"
    placeholder="Введите сферу" :error="errors.field_of_activity" v-model="field_of_activity" />
    <TextField :required="false" :ico="ClientIco" title="Вебсайт клиента"
    placeholder="Введите вебсайт клиента" :error="errors.website" v-model="website" />
    <TextField :required="false" :ico="ClientIco" title="Телефон клиента"
    placeholder="Введите телефон клиента" :error="errors.phone" v-model="phone" />
    <TextField :required="false" :ico="ClientIco" title="Email клиента"
    placeholder="Введите email клиента" :error="errors.email" v-model="email" />
    <TextField :required="false" :ico="ClientIco" title="Город клиента"
    placeholder="Введите город клиента" :error="errors.city" v-model="city" />
    <TextField :required="false" :ico="ClientIco" title="Канал коммуникации"
    placeholder="Введите канал коммуникации" :error="errors.channel" v-model="channel" />
  </CreateModalWrapper>
</template>

<script setup lang="ts">
  import ClientIco from '../../icons/Kanban/ClientIco.vue';
  import TextField from '../../CRM/modals/fields/TextField.vue';
  import CreateModalWrapper from '../../CRM/modals/CreateModalWrapper.vue';
  import { ref } from 'vue';

  const emit = defineEmits(['close', 'submit']);

  const name = ref('');
  const inn = ref('');
  const field_of_activity = ref('');
  const website = ref('');
  const phone = ref('');
  const email = ref('');
  const city = ref('');
  const channel = ref('');

  function submit() {
    const data = {
      name: name.value,
      inn: inn.value,
      field_of_activity: field_of_activity.value,
      website: website.value,
      phone: phone.value,
      email: email.value,
      city: city.value,
      channel: channel.value
    }
    emit("submit", data);
  }

  const props = defineProps<{
    error?: string | null,
    errors: Record<string, string>
  }>();
</script>