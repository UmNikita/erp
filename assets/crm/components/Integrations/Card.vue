<template>
  <div class="integrations-card">
    <div class="integrations-card__header">
      <div class="integrations-card__title-wrap">
        <div class="integrations-card__icon">
          <svg viewBox="0 0 24 24" fill="none">
              <path d="M8 9L4 12L8 15M16 9L20 12L16 15M14 5L10 19" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>

        <div>
          <h2 class="integrations-card__title">API-доступ</h2>
          <p class="integrations-card__subtitle">
          Используйте токен для авторизации внешних приложений.
          </p>
        </div>
      </div>

      <span class="api-status" v-if="token.is_active">Активен</span>
      <span class="api-status deactive" v-else>Диактивирован</span>
    </div>

    <div class="token-block">
      <span class="token-block__label">API-токен</span>

      <div class="token-block__row">
        <div class="token-block__value">{{ token.token }}</div>

        <button class="token-block__copy" @click="copyToken">
          <svg viewBox="0 0 24 24" fill="none">
          <rect x="8" y="8" width="11" height="11" rx="2" stroke="currentColor" stroke-width="1.7"/>
          <path d="M16 8V6A2 2 0 0 0 14 4H6A2 2 0 0 0 4 6V14A2 2 0 0 0 6 16H8" stroke="currentColor" stroke-width="1.7"/>
          </svg>
        </button>
      </div>

      <p class="token-block__warning">
        <svg viewBox="0 0 24 24" fill="none">
          <path d="M12 3L21 20H3L12 3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
          <path d="M12 9V14M12 17V17.1" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
        </svg>
        Полное значение токена не передавайте третьим лицам.
      </p>

      <div class="token-created">Новый токен создан. Скопируйте его сейчас — позже он будет скрыт.</div>
    </div>

    <div class="token-actions">
      <button class="integrations-button integrations-button--secondary"  @click="emit('reissue')">
        <svg viewBox="0 0 24 24" fill="none">
          <path d="M5 8V4M5 4H9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
          <path d="M5.8 6.2A8 8 0 1 1 4.5 14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
        </svg>
        Выпустить новый токен
      </button>

      <button class="integrations-button integrations-button--danger" @click="emit('revoke')" v-if="token.is_active">Отозвать токен</button>
      <button class="integrations-button integrations-button--active" @click="emit('active')" v-else>Активировать токен</button>
    </div>

    <div class="integrations-divider"></div>

    <h3 class="integrations-section-title">Разрешения токена</h3>

    <div class="permissions">
      <span class="permission">Сделки</span>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { Token } from '../../types/integration';

  const emit = defineEmits(['reissue', 'revoke', 'active']);

  const props = defineProps<{
    token: Token;
  }>();

  async function copyToken() {
    await navigator.clipboard.writeText(props.token.token);
  }

</script>

<style scoped> 
  .integrations-card {
    padding: 22px;
    background: #ffffff;
    border: 1px solid #e2e6ec;
    border-radius: 13px;
  }

  .integrations-card__header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 20px;
  }

  .integrations-card__title-wrap {
    display: flex;
    align-items: center;
    gap: 13px;
  }

  .integrations-card__icon {
    width: 42px;
    height: 42px;
    flex: 0 0 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1267f4;
    background: #edf4ff;
    border-radius: 10px;
  }

  .integrations-card__icon svg {
    width: 22px;
    height: 22px;
  }

  .integrations-card__title {
    margin: 0 0 5px;
    color: #171b24;
    font-size: 17px;
    font-weight: 700;
  }

  .integrations-card__subtitle {
    margin: 0;
    color: #7a8497;
    font-size: 12px;
    line-height: 1.45;
  }

  .api-status {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    min-height: 28px;
    padding: 0 10px;
    color: #198b50;
    background: #edf9f2;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 650;
    white-space: nowrap;
  }

  .api-status.deactive {
    color: #8b1919;
    background: #f9eded;
  }

  .api-status::before {
    content: "";
    width: 7px;
    height: 7px;
    background: currentColor;
    border-radius: 50%;
  }

  .token-block {
    padding: 16px;
    background: #f8f9fb;
    border: 1px solid #e5e8ed;
    border-radius: 11px;
  }

  .token-block__label {
    display: block;
    margin-bottom: 9px;
    color: #6e798c;
    font-size: 12px;
    font-weight: 600;
  }

  .token-block__row {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .token-block__value {
    min-width: 0;
    flex: 1;
    height: 44px;
    display: flex;
    align-items: center;
    padding: 0 14px;
    overflow: hidden;
    color: #303744;
    background: #ffffff;
    border: 1px solid #dce1e8;
    border-radius: 9px;
    font-family: "SFMono-Regular", Consolas, "Liberation Mono", monospace;
    font-size: 13px;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .token-block__copy {
    width: 44px;
    height: 44px;
    flex: 0 0 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    color: #526077;
    background: #ffffff;
    border: 1px solid #dce1e8;
    border-radius: 9px;
    cursor: pointer;
    transition: 0.18s ease;
  }

  .token-block__copy:hover {
    color: #1267f4;
    background: #edf4ff;
    border-color: #cddfff;
  }

  .token-block__copy svg {
    width: 19px;
    height: 19px;
  }

  .token-block__warning {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin: 11px 0 0;
    color: #7d6a41;
    font-size: 11px;
    line-height: 1.5;
  }

  .token-block__warning svg {
    width: 15px;
    height: 15px;
    flex: 0 0 15px;
    margin-top: 1px;
    color: #e09a23;
  }

  .token-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 16px;
  }

  .integrations-button {
    min-height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 0 16px;
    border-radius: 9px;
    font: inherit;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.18s ease;
  }

  .integrations-button svg {
    width: 17px;
    height: 17px;
  }

  .integrations-button--primary {
    color: #ffffff;
    background: #1267f4;
    border: 1px solid #1267f4;
    box-shadow: 0 6px 14px rgba(18, 103, 244, 0.18);
  }

  .integrations-button--primary:hover {
    background: #0959dc;
    border-color: #0959dc;
  }

  .integrations-button--secondary {
    color: #3f4755;
    background: #ffffff;
    border: 1px solid #dce1e8;
  }

  .integrations-button--secondary:hover {
    background: #f7f8fa;
  }

  .integrations-button--danger {
    margin-left: auto;
    color: #c44248;
    background: #fff6f6;
    border: 1px solid #f0d4d6;
  }

  .integrations-button--danger:hover {
    color: #ffffff;
    background: #e5484d;
    border-color: #e5484d;
  }

  .integrations-button--active {
    margin-left: auto;
    color: #4244c4;
    background: #f6f7ff;
    border: 1px solid #d4d5f0;
  }

  .integrations-button--active:hover {
    color: #f6f7ff;
    background: #4244c4;
    border-color: #d4d5f0;
  }

  .integrations-divider {
    height: 1px;
    margin: 22px 0;
    background: #e8ebf0;
  }

  .integrations-section-title {
    margin: 0 0 12px;
    color: #343b48;
    font-size: 13px;
    font-weight: 700;
  }

  .permissions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .permission {
    min-height: 32px;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 0 10px;
    color: #42506a;
    background: #f5f7fa;
    border: 1px solid #e3e7ed;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
  }

  .permission::before {
    content: "";
    width: 7px;
    height: 7px;
    background: #20b866;
    border-radius: 50%;
  }

  .token-created {
    display: none;
    margin-top: 14px;
    padding: 12px 13px;
    color: #187c49;
    background: #eefaf3;
    border: 1px solid #d3f0df;
    border-radius: 8px;
    font-size: 12px;
  }

  .token-created.show {
    display: block;
  }

  @media (max-width: 900px) {

    .token-actions {
      align-items: stretch;
      flex-direction: column;
    }
    
    .integrations-button--danger, .integrations-button--active {
      margin-left: 0;
    }

  }

  @media (max-width: 600px) {

    .token-block__row {
      align-items: stretch;
      flex-direction: column;
    }

    .token-block__copy {
      width: 100%;
    }
  }
</style>