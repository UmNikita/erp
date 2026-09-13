import { onMounted, ref } from 'vue';

export function useInlineRenameForm<T extends object, E extends object>(model: T) {

    const editing = ref(false);
    const errors = ref<E>({} as E);
    const data = ref<T>({... model});
    const generalError = ref<string | null>(null);

    function startEditing() {
        editing.value = true;
    }

    function cancelEditing() {
        generalError.value = null;
        errors.value = {} as E;
        editing.value = false;
        data.value = { ...model };
    }

    function setNewData() {
        Object.assign(model, data.value);
    }

    function accept(): Partial<T> | null {
        generalError.value = null;
        errors.value = {} as E;

        const newData: Partial<T> = {};

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