import { ref } from 'vue';

const activeModal = ref<string | null>(null);
const generalError = ref<string | null>(null);

export function useModal() {

    function openModal(name: string) {
        activeModal.value = name;
    }

    function closeModal() {
        activeModal.value = null;
        generalError.value = null;
    }
    return {
        activeModal,
        generalError,
        openModal,
        closeModal
    };
}

export const MODALS = {
    CREATE_PIPELINE: 'pipeline-create',
    SETTINGS_PIPELINE: 'pipeline-settings',
    CREATE_STAGE: 'stage-create',
    CREATE_LEAD: 'lead-create',
    CREATE_CLIENT: 'client-create',
    CREATE_CONTACT: 'contact-create',
    SEND_KP: 'send-kp',
} as const;;