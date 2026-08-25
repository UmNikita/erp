<template>
    <div class="integrations-card">
        <div class="integrations-card__header">
            <div>
                <h2 class="integrations-card__title">Информация</h2>
                <p class="integrations-card__subtitle">
                    Статус текущего API-токена.
                </p>
            </div>
        </div>

        <div class="api-info">
            <div class="api-info__item">
                <span class="api-info__label">Создан</span>
                <span class="api-info__value">{{ formatResponseDate(token.create_at) }}</span>
            </div>

            <div class="api-info__item">
                <span class="api-info__label">Кол-во запросов на добавление данных</span>
                <span class="api-info__value">{{ token.count_requests }}</span>
            </div>

            <div class="api-info__item">
                <span class="api-info__label">Версия API</span>
                <span class="api-info__value api-info__value--mono">v1</span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
  import { Token } from '../../types/integration';
  import { formatResponseDate } from '../../utils/fields';

  const props = defineProps<{
    token: Token;
  }>();

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

    .api-info {
      display: grid;
      gap: 14px;
    }

    .api-info__item {
      padding-bottom: 14px;
      border-bottom: 1px solid #edf0f4;
    }

    .api-info__item:last-child {
      padding-bottom: 0;
      border-bottom: none;
    }

    .api-info__label {
      display: block;
      margin-bottom: 6px;
      color: #7a8497;
      font-size: 11px;
      font-weight: 600;
    }

    .api-info__value {
      color: #343b48;
      font-size: 13px;
      font-weight: 600;
    }

    .api-info__value--mono {
      font-family: Consolas, monospace;
      font-size: 12px;
    }
</style>