<template>
  <div class="deal-top" v-if="pipelineStore.pipelines.length > 0">
    <router-link class="deal-top__back" to="/crm">
      <BackIco />
      Назад к воронке
    </router-link>

    <HeaderName :lead="props.lead" />

    <div class="deal-top__controls">
      <HeaderStage :lead="props.lead" />

      <!-- <div class="deal-top__communication">
        <button class="communication-button" type="button">
          <svg viewBox="0 0 24 24" fill="none"><path d="M7 4L10 8L8 11C9.4 14 10.8 15.4 13.8 16.8L17 15L20 18C20 19.1 19.1 20 18 20C10.3 19.5 4.5 13.7 4 6C4 4.9 4.9 4 6 4H7Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
          Позвонить
          <span class="communication-button__arrow">⌄</span>
        </button>

        <button class="communication-button" type="button">
          <svg viewBox="0 0 24 24" fill="none"><path d="M20 11.5A8 8 0 0 1 8.3 18.6L4 20L5.4 15.7A8 8 0 1 1 20 11.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
          Написать
          <span class="communication-button__arrow">⌄</span>
        </button>
      </div> -->
    </div>
  </div>
</template>

<script setup lang="ts">
  import { LeadDetail } from '../../../types/lead.ts';
  import BackIco from '../../icons/BackIco.vue';
  import {usePipelineStore} from '../../../stores/pipelines.ts';
  import HeaderName from './HeaderName.vue';
  import HeaderStage from './HeaderStage.vue';

  const pipelineStore = usePipelineStore();

  const props = defineProps<{
    lead: LeadDetail;
  }>();
</script>

<style scoped> 

  .accept {
    margin-right: 15px;
  }
  .general-err {
    margin: 6px 0 0;
    padding: 4px 6px 4px 16px;

    color: #c9363e;
    background: #fff5f5;
    border: 1px solid #f2c9cc;
    border-radius: 8px;

    font-size: 10px;
    font-weight: 500;
  }

  .deal-top {
    padding: 22px 28px;
    background: #fff;
    border: 1px solid #e4e8ee;
    border-radius: 14px;
    box-shadow: 0 3px 12px rgba(28, 39, 56, .04);
    display: block;
  }

  .deal-top__back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 13px;
    padding: 0;
    color: #1267f4;
    background: transparent;
    border: 0;
    font-size: 12px;
    font-weight: 600;
  }

  .deal-top__back svg {
    width: 16px;
    height: 16px;
  }

  .deal-top__controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
  }

  .deal-top__communication {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .stage-dot {
    width: 8px;
    height: 8px;
    background: #16a565;
    border-radius: 50%;
  }

  .deal-select__icon {
    width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #4e627d;
  }

  .deal-select__icon svg {
    width: 18px;
    height: 18px;
  }

  .deal-select__value {
    font-size: 12px;
    font-weight: 650;
  }

  .deal-select__arrow {
    width: 15px;
    height: 15px;
    margin-left: 2px;
    color: #4c5c73;
  }

  .communication-button {
    height: 40px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 0 14px;
    color: #273247;
    background: #fff;
    border: 1px solid #dce2ea;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
  }

  .communication-button:hover {
    background: #f8f9fb;
  }

  .communication-button svg {
    width: 18px;
    height: 18px;
  }

  .communication-button__arrow {
    padding-left: 11px;
    margin-left: 4px;
    border-left: 1px solid #e1e5eb;
  }

  @media (max-width: 900px) {

  .deal-top__controls {
    align-items: flex-start;
    flex-direction: column;
  }

  .deal-top__communication {
    width: 100%;
  }
  }

  @media (max-width: 700px) {

  .deal-top {
    padding: 18px 15px;
  }

  .communication-button {
    flex: 1;
    justify-content: center;
  }
  }
</style>