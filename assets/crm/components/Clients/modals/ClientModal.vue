<template>
  <CreateModalWrapper :accepting="accepting" title-btn="Создать" title="Создание клиента" subtitle="Заполните информацию о клиенте"
  :error="generalError" @submit="submit" @close="emit('close')">
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
  import { ClientErrors } from '../../../types/client.ts';
  import { validateClient } from '../../../validators/client.ts';
  import { useClientTableStore } from '../../../stores/clientTable.ts';

  const emit = defineEmits(['close', 'submit']);

  const clientTableStore = useClientTableStore();
  
  const props = defineProps<{
    isSubmit: boolean
  }>();


  const accepting = ref(false);
  const generalError = ref<string | null>(null);
  const errors = ref<ClientErrors>({
    name: null,
    inn: null,
    field_of_activity: null,
    website: null,
    phone: null,
    email: null,
    city: null,
    channel: null
  });

  const name = ref('');
  const inn = ref('');
  const field_of_activity = ref('');
  const website = ref('');
  const phone = ref('');
  const email = ref('');
  const city = ref('');
  const channel = ref('');

  async function submit() {
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
    const validationErrors = validateClient(data);
    if (!validationErrors.isValid) {
      errors.value = validationErrors.errors;
      return false;
    }
    accepting.value = true;
    if(props.isSubmit) {
      emit('submit', data);
    }
    else {
      try {
        await clientTableStore.createClient(data);
      }
      catch {
        generalError.value = 'Не удалось создать клиента. Попробуйте чуть позже';
        return;
      }
      finally {
        accepting.value = false;
      }
    }
    emit('close');
  }
</script>