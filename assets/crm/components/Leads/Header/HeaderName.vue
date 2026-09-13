<template>
    <div class="deal-top__title-row">
      <div class="deal-top__title-wrap">
        <RenameField title-field="" :value-field="lead.name" 
          class="deal-top__title" :editing="nameForm.editing.value" :error="nameForm.errors.value.name" v-model="nameForm.data.value.name" />

        <button v-if="!nameForm.editing.value" @click="nameForm.startEditing" class="deal-top__edit">
          <EditIco />
        </button>
        <div v-else>
          <button class="link accept" @click="acceptEditingName">Принять</button>
          <button class="link" @click="nameForm.cancelEditing">Отменить</button>
        </div>
      </div>

      <button class="deal-top__favorite">
        <svg viewBox="0 0 24 24" fill="none"><path d="M12 3L14.8 8.7L21 9.6L16.5 14L17.6 20.2L12 17.3L6.4 20.2L7.5 14L3 9.6L9.2 8.7L12 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
      </button>
    </div>
</template>
<script setup lang="ts">
  import { useInlineRenameForm } from '../../../composables/useInlineRenameForm.ts';
  import { LeadDetail } from '../../../types/lead.ts';
  import RenameField from '../../common/RenameField.vue';
  import { ref } from 'vue';
  import { renameLead } from '../../../api/lead.ts';
  import EditIco from '../../icons/EditIco.vue';
  import { validateLeadName } from '../../../validators/lead.ts';
  import { useKanbanStore } from '../../../stores/kanban.ts';
  
  const kanbanStore = useKanbanStore();

  const props = defineProps<{
    lead: LeadDetail;
  }>();

  const isClickedRenameBtn = ref(false);
  const nameForm = useInlineRenameForm<{name: string}, {name: string | null}>(props.lead);
  
  async function acceptEditingName() {
    if(isClickedRenameBtn.value)
      return;
    isClickedRenameBtn.value = true;
    const newData = nameForm.accept();
    if(!newData || !props.lead) {
      isClickedRenameBtn.value = false;
      return;
    }
      
    const validateRes = validateLeadName(newData.name);
    if(!validateRes.isValid) {
      nameForm.errors.value.name = validateRes.errors.name;
      isClickedRenameBtn.value = false;
      return;
    }
    try {
      const res = await renameLead(props.lead.id, nameForm.data.value.name);
      if(res) {
        nameForm.editing.value = false;
        nameForm.setNewData();
      }
      kanbanStore.clear();
    }
    catch {
      alert("Не удалось переименовать сделку! Попробуйте позже");
    }
    finally {
      isClickedRenameBtn.value = false;
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

  .deal-top__title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 17px;
  }

  .deal-top__title-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .deal-top__title {
    margin: 0;
    font-size: 27px;
    line-height: 1.2;
    font-weight: 700;
    letter-spacing: -.5px;
  }

  :deep(.deal-top__title input) {
    border: none;
    outline: none;
    border-bottom: 1px solid #303744;
    width: 200px;
  }

  :deep(.err) {
    color: #d93d42;
    line-height: 1.4;
    font-weight: 500;
    font-size: 12px;
  }

  .deal-top__edit,
  .deal-top__favorite {
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    color: #465267;
    background: transparent;
    border: 0;
    width: 25px;
    height: 25px;
    border-radius: 6px;
  }
  
  .deal-top__edit:hover {
    background: #f1f5fa;
  }

  .deal-top__edit svg {
    width: 17px;
    height: 17px;
  }

  .deal-top__favorite svg {
    width: 21px;
    height: 21px;
  }

  @media (max-width: 700px) {
    .deal-top__title {
        font-size: 23px;
    }
  }
</style>