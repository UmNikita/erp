<template>
    <div class="stages-empty">
        <div class="funnels-empty__icon">
            <StageIco />
        </div>

        <h2 class="funnels-empty__title">Этапов нет</h2>
        <p class="funnels-empty__text">Создайте первый этап, чтобы добавлять сделки.</p>

        <button class="funnels-empty__button" @click="openModal(MODALS.CREATE_STAGE)">
            <span>+</span>
            Создать этап
        </button>
    </div>
</template>

<script setup lang="ts">
  import { MODALS, useModal } from '../../../../composables/useModal.ts';
  import StageIco from '../../../icons/Kanban/StageIco.vue';

  const { openModal } = useModal();

</script>

<style>
.stages-empty {
  width: calc(100% - 44px);
  min-height: 420px;
  margin: 24px 22px;
  padding: 50px 24px;

  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;

  text-align: center;
}
</style>