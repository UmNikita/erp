import { nextTick, ref } from 'vue';
import { useKanbanStore } from '../../../stores/kanban.ts';

export function useRenameStage(stageName: string) {

    const editingName = ref<boolean>(false);
    const name = ref(stageName);
    const nameInput = ref<HTMLInputElement | null>(null);
    const kanbanStore = useKanbanStore();

    async function acceptRenameStage(prop: any, value: string) {
        const oldName = prop.stage.name;
        try {
            prop.stage.name = value;
            await kanbanStore.renameStage(prop.stage.id, value);
        }
        catch {
            alert("Не удалось обновить название. Попробуйте позже!")
            prop.stage.name = oldName;
        }
    }

    async function startEditName(props: any) {
        name.value = props.stage.name;
        editingName.value = true;

        await nextTick();

        nameInput.value?.focus();
    }

    async function saveName(props: any) {
        if (!editingName.value) {
            return;
        }
        
        if (!name.value.trim()) {
            name.value = props.stage.name;
            editingName.value = false;
            return;
        }

        editingName.value = false;

        if (name.value !== props.stage.name) {
            acceptRenameStage(props, name.value);
        }
    }

    function cancelEdit(props: any) {
        name.value = props.stage.name;
        editingName.value = false;
    }

    return {
        editingName,
        name,
        nameInput,
        startEditName,
        saveName,
        cancelEdit
    };
}