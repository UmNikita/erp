<template>
    <div class="funnels-empty">
        <div class="funnels-empty__icon">
            <PipelineIco />
        </div>

        <h2 class="funnels-empty__title">Воронок нет</h2>

        <p class="funnels-empty__text">Создайте первую воронку, чтобы добавлять сделки и распределять их по этапам.</p>

        <button class="funnels-empty__button" @click="emit('openPipelineModal')">
            <span>+</span>
            Создать воронку
        </button>
    </div>
</template>

<script setup lang="ts">
import PipelineIco from '../../../icons/PipelineIco.vue';

  const emit = defineEmits<{
    openPipelineModal: []
  }>();
</script>

<style>
.funnels-empty {
  width: calc(100% - 44px);
  min-height: 420px;
  margin: 24px 22px;
  padding: 50px 24px;

  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;

  text-align: center;
  background: #ffffff;
  border: 1px solid #e2e6ec;
  border-radius: 16px;
  box-shadow: 0 4px 16px rgba(26, 39, 60, 0.06);
}

.funnels-empty__icon {
  width: 72px;
  height: 72px;
  margin-bottom: 22px;

  display: flex;
  align-items: center;
  justify-content: center;

  color: #0b63f6;
  background: #eef5ff;
  border: 1px solid #dceaff;
  border-radius: 20px;
}

.funnels-empty__icon svg {
  width: 34px;
  height: 34px;
}

.funnels-empty__title {
  margin: 0 0 10px;
  color: #151922;
  font-size: 24px;
  font-weight: 650;
}

.funnels-empty__text {
  max-width: 430px;
  margin: 0 0 26px;
  color: #687181;
  font-size: 15px;
  line-height: 1.6;
}

.funnels-empty__button {
  height: 48px;
  padding: 0 22px;

  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;

  color: #ffffff;
  background: #0b63f6;
  border: none;
  border-radius: 11px;
  box-shadow: 0 7px 16px rgba(11, 99, 246, 0.2);

  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s ease;
}

.funnels-empty__button span {
  font-size: 23px;
  font-weight: 400;
  line-height: 1;
}

.funnels-empty__button:hover {
  background: #0956d8;
  transform: translateY(-1px);
  box-shadow: 0 9px 20px rgba(11, 99, 246, 0.26);
}

@media (max-width: 700px) {
  .funnels-empty {
    width: calc(100% - 24px);
    min-height: 360px;
    margin: 12px;
    padding: 35px 20px;
  }

  .funnels-empty__title {
    font-size: 21px;
  }
}
</style>