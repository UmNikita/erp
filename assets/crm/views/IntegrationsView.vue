<template>
  <div class="integrations-page" v-if="loading">
    <template v-if="!error">
      <div class="integrations-page__header">
        <div>
          <h1 class="integrations-page__title">Интеграции</h1>
          <p class="integrations-page__description">Подключайте внешние сервисы и используйте API для работы с CRM.</p>
        </div>
      </div>
      <Integrations :token="token" @set-token="setToken" v-if="token" />
      <div class="integrations-page__grid" v-else>
        <button class="primary" @click="createToken">
          <PlusIco /> Создать интеграцию
        </button>
      </div>
    </template>
    <Error v-else />
  </div>
</template>

<script setup lang="ts">
  import { onMounted, ref } from 'vue';
  import Integrations from '../components/Integrations/Integrations.vue';
  import { createToken as createTokenApi, getTokens } from '../api/integration.ts';
  import PlusIco from '../components/icons/PlusIco.vue';
  import Error from '../components/Error.vue';
  import { Token } from '../types/integration.ts';

  const loading = ref(false);
  const error = ref(false);
  const token = ref<Token | null>(null);

  async function createToken() {
    try{
      const res = await createTokenApi();
      token.value = res;
    }
    catch {
      alert("Возникла ошибка!");
    }
  }

  function setToken(value: Token) {
    token.value = value;
  }

  onMounted(async ()=> {
    try{
      const res = await getTokens();
      if(res.length == 0) {
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
    .integrations-page__grid {
      display: grid;
      grid-template-columns: minmax(0, 1.5fr) minmax(280px, 0.5fr);
      gap: 18px;
    }
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

    @media (max-width: 900px) {
      .integrations-page__grid {
        grid-template-columns: 1fr;
      }
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