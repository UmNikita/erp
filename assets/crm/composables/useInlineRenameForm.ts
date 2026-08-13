import { onMounted, ref } from 'vue';

export function useInlineRenameForm(model: Record<string, string>) {

    const editing = ref(false);
    const errors = ref<Record<string, string>>({});
    const data = ref<Record<string, string>>({});
    const generalError = ref();

    function startEditing() {
        editing.value = true;
    }

    function cancelEditing() {
        generalError.value = null;
        errors.value = {};
        editing.value = false;
        data.value = { ...model };
    }

    function setNewData() {
        Object.assign(model, data.value);
    }

    function accept(): Record<string, unknown> | null {
        generalError.value = null;
        errors.value = {};
        const newData: Record<string, unknown> = {};

        for (const key in model) {
            if (data.value[key] !== model[key]) {
                newData[key] = data.value[key];
            }
        }

        if (Object.keys(newData).length === 0) {
            editing.value = false;
            return null;
        }
        return newData;
    }

    onMounted(() => {
        data.value = { ...model };
    });

    return {
        editing,
        errors,
        data,
        generalError,
        startEditing,
        cancelEditing,
        accept,
        setNewData
    };
}