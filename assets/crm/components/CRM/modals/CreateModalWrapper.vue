<template>
  <div class="modal-overlay" @click.self="close">
    <div class="create-pipeline">
        <div class="create-pipeline__header">
          <h1>{{ title }}</h1>
          <p>{{ subtitle }}</p>
        </div>

        <div class="create-pipeline__fields">
          <slot />
        </div>

        <p v-if="props.error" class="create-pipeline__general-error">{{ props.error }}</p>

        <div class="create-pipeline__actions">
          <button class="create-pipeline__cancel" @click.self="close">Отмена</button>
          <button class="create-pipeline__submit" @click="submit">Создать</button>
        </div>
      </div>
  </div>
</template>

<script setup lang="ts">

    const emit = defineEmits(['close', 'submit']);

    const props = defineProps<{
        error?: string | null,
        title: string,
        subtitle: string
    }>();

    function close() {
        emit('close');
    }

    function submit() {
        emit('submit');
    }

</script>

<style scoped>

.create-pipeline__general-error {
  margin: 16px 0 0;
  padding: 10px 13px 10px 38px;
  position: relative;

  color: #c9363e;
  background: #fff5f5;
  border: 1px solid #f2c9cc;
  border-radius: 8px;

  font-size: 12px;
  font-weight: 500;
  line-height: 1.45;
}

.create-pipeline__general-error::before {
  content: "!";
  position: absolute;
  top: 50%;
  left: 13px;
  width: 16px;
  height: 16px;

  display: flex;
  align-items: center;
  justify-content: center;
  transform: translateY(-50%);

  color: #ffffff;
  background: #e5484d;
  border-radius: 50%;

  font-size: 11px;
  font-weight: 700;
}

.create-pipeline {
  width: 100%;
  max-width: 700px;
  padding: 36px;
  background: #ffffff;
  color: #171b24;
  font-family: Inter, Arial, sans-serif;
  box-sizing: border-box;
  border-radius: 15px;
}

.create-pipeline__header {
  margin-bottom: 32px;
}

.create-pipeline__header h1 {
  margin: 0 0 8px;
  font-size: 29px;
  line-height: 1.2;
  font-weight: 700;
}

.create-pipeline__header p {
  margin: 0;
  color: #778197;
  font-size: 14px;
}

.create-pipeline__actions {
  display: flex;
  justify-content: flex-end;
  gap: 16px;
  margin-top: 40px;
}

.create-pipeline__actions button {
  min-width: 120px;
  height: 46px;
  padding: 0 24px;
  border-radius: 8px;
  font: inherit;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
}

.create-pipeline__cancel {
  border: 1px solid #dfe3e9;
  background: #ffffff;
  color: #242936;
}

.create-pipeline__submit {
  border: none;
  background: #1267f4;
  color: #ffffff;
  box-shadow: 0 7px 16px rgba(18, 103, 244, 0.2);
}

.create-pipeline__submit:hover {
  background: #0959dc;
}

@media (max-width: 700px) {
  .create-pipeline {
    padding: 24px 16px;
  }

  .create-pipeline__actions {
    flex-direction: column-reverse;
  }

  .create-pipeline__actions button {
    width: 100%;
  }
}

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>