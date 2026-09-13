<template>
  <div class="integrations-page__grid">
    <Card v-if="token" :token="token" @set-token="setToken" />
    <Information v-if="token" :token="token" />
    <Documentation v-if="token" :token="token" />
  </div>
</template>

<script setup lang="ts">
  import Card from './Card.vue';
  import Documentation from './Documentation.vue';
  import Information from './Information.vue';
  import { Token } from '../../types/integration.ts';

  const emit = defineEmits(['setToken']);

  const props = defineProps<{
    token: Token
  }>();

  function setToken(value: Token) {
    emit('setToken', value);
  }

</script>