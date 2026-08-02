import { nextTick, ref } from 'vue';
import { renameStage } from '../../api/stage';

export function useRenameStage(stageName: string) {

    const editingName = ref<boolean>(false);
    const name = ref(stageName);
    const nameInput = ref<HTMLInputElement | null>(null);

    async function startEditName(props: any) {
        name.value = props.stage.name;
        editingName.value = true;

        await nextTick();

        nameInput.value?.focus();
    }

    async function saveName(props: any, emit: any) {
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
            emit("rename", props, name.value);
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