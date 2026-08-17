<template>
    <div class="documentation">
        <div class="documentation__header">
            <div>
                <h2 class="documentation__title">Документация API</h2>
                <p class="documentation__description">Основные методы для работы с CRM через внешний сервис.</p>
            </div>
        </div>

        <div class="documentation__base">
            <span class="documentation__base-label">Base URL</span>
            <code>{{ baseURL }}</code>
        </div>

        <div class="documentation__grid">
            <div class="endpoints">
              <button class="endpoint" :class="route.route == currentRoute.route ? 'active' : ''" v-for="route in api.api" @click="setCurrentRoute(route)">
                  <span class="endpoint__method endpoint__method--post">{{ route.method }}</span>
                  <span class="endpoint__path">{{ route.route }}</span>
              </button>
            </div>

            <div class="documentation__content">
              <h3>{{ currentRoute.title }}</h3>
              <p>{{ currentRoute.desctiption }}</p>

              <div class="request-body" v-if="currentRoute.body">
                  <span class="request-body__label">Body</span>
                  <pre>{{ JSON.stringify(currentRoute.body, null, 2) }}</pre>
              </div>

              <div class="code-block">
                  <span class="code-block__label">Пример запроса</span>
                  <pre>
<code>curl -X {{ currentRoute.method }} "{{ baseURL + currentRoute.route }}" \
  -H "Authorization: Bearer {{ token.token }}" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  <span v-if="currentRoute.body">\
  -d '{{ JSON.stringify(currentRoute.bodyExample, null, 2) }}'</span>
</code>
                  </pre>
              </div>

              <div class="docs-note">
                  Передавайте токен в заголовке <strong>Authorization</strong> в формате <strong>Bearer TOKEN</strong>.
              </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
  import { ref } from 'vue';
  import { apiv1 as api, baseURL } from '../../apiDoc';
  import { Token } from '../../types/integration';

  const currentRoute = ref(api.api[0]);

  const props = defineProps<{
    token: Token;
  }>();

  function setCurrentRoute(route: any) {
    currentRoute.value = route;
  }

</script>

<style scoped> 
  .documentation {
    margin-top: 18px;
    padding: 22px;
    background: #ffffff;
    border: 1px solid #e2e6ec;
    border-radius: 13px;
  }

  .documentation__header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 22px;
  }

  .documentation__title {
    margin: 0 0 6px;
    font-size: 19px;
    font-weight: 700;
  }

  .documentation__description {
    margin: 0;
    color: #758097;
    font-size: 13px;
  }

  .documentation__base {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 22px;
    padding: 13px 15px;
    background: #f8f9fb;
    border: 1px solid #e4e8ee;
    border-radius: 9px;
  }

  .documentation__base-label {
    flex: 0 0 auto;
    color: #7a8497;
    font-size: 11px;
    font-weight: 600;
  }

  .documentation__base code {
    min-width: 0;
    overflow: hidden;
    color: #303744;
    font-family: Consolas, monospace;
    font-size: 12px;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .documentation__grid {
    display: grid;
    grid-template-columns: minmax(220px, 0.45fr) minmax(0, 1fr);
    gap: 20px;
  }

  .endpoints {
    display: flex;
    flex-direction: column;
    gap: 7px;
  }

  .endpoint {
    width: 100%;
    min-height: 45px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 11px;
    color: #465168;
    background: #ffffff;
    border: 1px solid #e4e8ee;
    border-radius: 8px;
    text-align: left;
    cursor: pointer;
    transition: 0.16s ease;
  }

  .endpoint:hover,
  .endpoint.active {
    color: #1267f4;
    background: #f4f8ff;
    border-color: #cbdcff;
  }

  .endpoint__method {
    min-width: 43px;
    color: #16864e;
    font-family: Consolas, monospace;
    font-size: 10px;
    font-weight: 700;
  }

  .endpoint__method--post {
    color: #1267f4;
  }

  .endpoint__method--patch {
    color: #c07a18;
  }

  .endpoint__method--delete {
    color: #d34147;
  }

  .endpoint__path {
    overflow: hidden;
    font-family: Consolas, monospace;
    font-size: 11px;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .documentation__content {
    min-width: 0;
  }

  .documentation__content h3 {
    margin: 0 0 8px;
    font-size: 15px;
  }

  .documentation__content > p {
    margin: 0 0 15px;
    color: #6e788b;
    font-size: 12px;
    line-height: 1.5;
  }

  .code-block {
    position: relative;
    overflow-x: auto;
    padding: 17px;
    color: #d8e1f2;
    background: #171d28;
    border-radius: 10px;
  }

  .code-block pre {
    margin: 0;
    font-family: Consolas, monospace;
    font-size: 12px;
    line-height: 1.65;
    white-space: pre;
  }

  .code-block__label {
    display: block;
    margin-bottom: 11px;
    color: #818da3;
    font-size: 10px;
    font-family: Inter, Arial, sans-serif;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .docs-note {
    margin-top: 14px;
    padding: 11px 13px;
    color: #536077;
    background: #f8f9fb;
    border-left: 3px solid #1267f4;
    border-radius: 0 8px 8px 0;
    font-size: 11px;
    line-height: 1.5;
  }

  .request-body {
    margin-bottom: 18px;
    padding: 16px;
    background: #f8f9fb;
    border: 1px solid #e2e6ec;
    border-radius: 10px;
  }

  .request-body__label {
    display: block;
    margin-bottom: 10px;
    color: #7a8497;
    font-size: 10px;
    font-weight: 700;
    font-family: Inter, Arial, sans-serif;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .request-body pre {
    margin: 0;
    overflow-x: auto;
    color: #303744;
    font-family: Consolas, monospace;
    font-size: 12px;
    line-height: 1.6;
    white-space: pre;
  }

  @media (max-width: 900px) {
    .documentation__grid {
      grid-template-columns: 1fr;
    }
  }
</style>