<template>
    <div class="integrations-page" v-if="loading">
      <div v-if="error"><Error /></div>
      <template v-else>
        <div class="integrations-page__header">
            <div>
            <h1 class="integrations-page__title">Интеграции</h1>
            <p class="integrations-page__description">Подключайте внешние сервисы и используйте API для работы с CRM.</p>
            </div>
        </div>

        <div class="integrations-page__grid" v-if="hasTokens">
          <Card v-if="token" :token="token" @reissue="reissueToken" @revoke="revokeToken" @active="activeToken" />
          <Information v-if="token" :token="token" />
        </div>
        <div class="integrations-page__grid" v-else>
          <button class="primary" @click="createToken">
            <PlusIco />
              Создать интеграцию
            </button>
        </div>
        <Documentation v-if="token" :token="token" />
      </template>
    </div>
</template>

<script setup lang="ts">
  import { onMounted, ref } from 'vue';
  import Card from './Card.vue';
  import Documentation from './Documentation.vue';
  import Information from './Information.vue';
  import PlusIco from '../icons/PlusIco.vue';
  import Error from '../Error.vue';
  import { getTokens, createToken as createTokenApi, reissue, revoke, active } from '../../api/integration.ts';

  const hasTokens = ref(true);
  const token = ref();
  const loading = ref(false);
  const error = ref(false);

  async function createToken() {
    try{
      const res = await createTokenApi();
      token.value = res;
      hasTokens.value = true;
    }
    catch {
      alert("Возникла ошибка!");
    }
  }

  async function reissueToken() {
    try{
      if(!confirm("Вы уверены что хотите обновить токен?"))
        return
      const res = await reissue(token.value.id);
      token.value = res;
    }
    catch {
      alert("Возникла ошибка!");
    }
  }

  async function revokeToken() {
    try{
      if(!confirm("Вы уверены что хотите отозвать токен?"))
        return
      const res = await revoke(token.value.id);
      token.value = res;
    }
    catch {
      alert("Возникла ошибка!");
    }
  }
  
  async function activeToken() {
    try{
      if(!confirm("Вы уверены что хотите активировать токен?"))
        return
      const res = await active(token.value.id);
      token.value = res;
    }
    catch {
      alert("Возникла ошибка!");
    }
  }

  onMounted(async ()=> {
    try{
      const res = await getTokens();
      if(res.length == 0) {
        hasTokens.value = false;
        return;
      }
      token.value = res[0];
    }
    catch {
        error.value = true;
    }
    finally {
        loading.value = true;
    }
  })

</script>

<style scoped>
    .integrations-page {
      width: 100%;
      min-height: 100%;
      padding: 30px;
      background: #ffffff;
      color: #171b24;
      font-family: Inter, Arial, sans-serif;
      box-sizing: border-box;
    }

    .integrations-page *,
    .integrations-page *::before,
    .integrations-page *::after {
      box-sizing: border-box;
    }

    .integrations-page__header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 24px;
      margin-bottom: 28px;
    }

    .integrations-page__title {
      margin: 0 0 7px;
      font-size: 29px;
      line-height: 1.2;
      font-weight: 700;
      letter-spacing: -0.4px;
    }

    .integrations-page__description {
      max-width: 620px;
      margin: 0;
      color: #758097;
      font-size: 14px;
      line-height: 1.5;
    }

    .integrations-page__grid {
      display: grid;
      grid-template-columns: minmax(0, 1.5fr) minmax(280px, 0.5fr);
      gap: 18px;
    }

    .primary {
        height: 48px;
        width: 250px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 0 20px;
        border: 0;
        border-radius: 10px;
        background: #1267f4;
        color: #fff;
        font-weight: 600;
        box-shadow: 0 7px 16px rgba(18, 103, 244, .2);
        cursor: pointer;
    }

    .primary svg {
        width: 20px;
    }

    @media (max-width: 900px) {
      .integrations-page {
        padding: 20px 14px;
      }

      .integrations-page__grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 600px) {
      .integrations-page__header,
      .documentation__header {
        flex-direction: column;
      }
    }
</style>