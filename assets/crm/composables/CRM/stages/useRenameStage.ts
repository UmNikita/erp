import { nextTick, ref } from 'vue';
import { renameStage  } from '../../../api/stage.ts';
import { usePipelines } from '../pipelines/usePipelines.ts';

export function useRenameStage(stageName: string, pipelineId: number) {

    const {renameStageForDetailPipeline} = usePipelines();

    const editingName = ref<boolean>(false);
    const name = ref(stageName);
    const nameInput = ref<HTMLInputElement | null>(null);

    async function acceptRenameStage(prop: any, value: string, pipelineId: number) {
        const oldName = prop.stage.name;
        try {
            prop.stage.name = value;
            await renameStage(value, prop.stage.id);
        }
        catch {
            alert("Не удалось обновить название. Попробуйте позже!")
            prop.stage.name = oldName;
        }
        renameStageForDetailPipeline(pipelineId, prop.stage.id, value);
    }

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
            acceptRenameStage(props, name.value, pipelineId);
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