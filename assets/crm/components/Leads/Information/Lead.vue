<template>
  <div class="info-card">
    <div class="info-card__header">
      <span class="info-card__title">Информация о сделке</span>
      <button v-if="!editing" @click="startEditing" class="info-card__edit">
        <EditIco />
      </button>
      <div v-else>
        <button class="link accept" @click="acceptEditing">Принять</button>
        <button class="link" @click="cancelEditing">Отменить</button>
      </div>
    </div>

    <RenameNumberField title-field="Бюджет" :value-field="lead.budget" 
      class="info-row" :editing="editing" :error="errors.budget" v-model="data.budget" />
    
    <RenameField title-field="Продукт / Услуга" :value-field="lead.product" 
      class="info-row" :editing="editing" :error="errors.product" v-model="data.product" />

    <RenameField title-field="Источник" :value-field="lead.source" 
      class="info-row" :editing="editing" :error="errors.source" v-model="data.source" />

    <RenameField title-field="Следующее действие" :value-field="lead.next_action" 
      class="info-row" :editing="editing" :error="errors.next_action" v-model="data.next_action" />

    <RenameField title-field="Комментарий" :value-field="lead.comment" 
      class="info-row" :editing="editing" :error="errors.comment" v-model="data.comment" />

    <p class="general-err" v-if="generalError">{{generalError}}</p>
  </div>
</template>

<script setup lang="ts">
  import { LeadDetail, LeadErrors, LeadRequest } from '../../../types/lead.ts';
  import { useInlineRenameForm } from '../../../composables/useInlineRenameForm.ts';
  import RenameField from '../../common/RenameField.vue';
  import RenameNumberField from '../../common/RenameNumberField.vue';
  import EditIco from '../../icons/EditIco.vue';
  import { validateLeadEdit } from '../../../validators/lead.ts';
  import { updateLead } from '../../../api/lead.ts';
  import { useKanbanStore } from '../../../stores/kanban.ts';

  const props = defineProps<{
    lead: LeadDetail;
  }>();

  const kanbanStore = useKanbanStore();

  const {editing, errors, data, generalError, 
    startEditing, cancelEditing, accept, setNewData} = useInlineRenameForm<LeadRequest, LeadErrors>(props.lead);
  
  async function acceptEditing() {
    const newData = accept();
    if(newData == null || !props.lead)
      return;
    const validationErrors = validateLeadEdit(data.value);
    if (!validationErrors.isValid) {
      errors.value = validationErrors.errors;
      return;
    }
    try {
      const res = await updateLead(props.lead.id, data.value);
      if(res) {
        editing.value = false;
        setNewData();
        kanbanStore.clear();
      }
    } catch {
      alert("Не удалось изменить сделку! Попробуйте позже");
    }
  }

</script>

<style scoped>
  .link {
    padding: 0;
    border: 0;
    background: transparent;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
  }

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

  .info-card {
    min-height: 255px;
    padding: 15px;
    background: #fff;
    border: 1px solid #dde2e9;
    border-radius: 10px;
  }

  .info-card__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 14px;
  }

  .info-card__title {
    color: #214982;
    font-size: 12px;
    font-weight: 700;
  }

  .info-card__edit {
    cursor: pointer;
    width: 25px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    color: #46617f;
    background: transparent;
    border: 0;
    border-radius: 6px;
  }

  .info-card__edit:hover {
    background: #f1f5fa;
  }

  .info-card__edit svg {
    width: 15px;
    height: 15px;
  }

  .info-row {
    display: grid;
    grid-template-columns: 122px minmax(0, 1fr);
    gap: 8px;
    margin-bottom: 10px;
    font-size: 10.5px;
    line-height: 1.45;
  }

  .info-row:last-child {
    margin-bottom: 0;
  }

  .info-row__label {
    color: #65748a;
  }

  :deep(.info-row input) {
    border: none;
    outline: none;
    border-bottom: 1px solid #303744;
    width: 150px;
  }

  :deep(.err) {
    color: #d93d42;
    line-height: 1.4;
    font-weight: 500;
  }

  .info-row__value {
    min-width: 0;
    color: #263246;
    font-weight: 500;
    word-break: break-word;
  }

  @media (max-width: 700px) {
    .info-card {
      min-height: 0;
    }

    .info-row {
      grid-template-columns: 105px minmax(0, 1fr);
    }
  }
</style>