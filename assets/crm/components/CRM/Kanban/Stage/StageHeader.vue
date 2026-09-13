<template>
    <header class="column-header">
        <div class="column-header-title">
            <h2 v-if="!editingName" class="column-title">{{stage.name}}</h2>
            <input
                v-else
                ref="nameInput"
                v-model="name"
                class="column-inp"
                @keyup.enter="saveName(props)"
                @keyup.esc="cancelEdit(props)"
                @blur="saveName(props)"
            />
            <StageMenu @rename="startEditName(props)" @delete="deleteStage"
            @forward="moveForwardStage(props.stage)" @back="moveBackStage(props.stage)" />
        </div>
        <div class="column-meta">
            <span>{{stage.leadCount}} {{getPluralizeLead(stage.leadCount)}}</span>
            <span>{{stage.moneyAmount}} ₽</span>
        </div>
    </header>
</template>

<script setup lang="ts">
    import { useMoveStage } from '../../../../composables/CRM/stages/useMoveStage';
    import { useRenameStage } from '../../../../composables/CRM/stages/useRenameStage';
    import { useKanbanStore } from '../../../../stores/kanban';
    import { StageUI } from '../../../../types/stage';
    import { getPluralizeLead } from '../../../../utils/words';
    import StageMenu from './StageMenu.vue';

    const props = defineProps<{ stage: StageUI }>();

    const { moveForwardStage, moveBackStage } = useMoveStage();
    const {editingName, name, nameInput,
    startEditName, cancelEdit, saveName} = useRenameStage(props.stage.name)
    const kanbanStore = useKanbanStore();

    async function deleteStage() {
        if(!confirm(`Вы действительно хотите удалить этап "${props.stage.name}"`))
            return;
        if(props.stage.leadCount > 0) {
            alert("Нельзя удалить этап с лидами");
            return;
        }
        try {
            await kanbanStore.deleteStage(props.stage.id);
        }
        catch {
            alert("Не удалось удалить. Попробуйте позже!");
            return;
        }
    }
</script>