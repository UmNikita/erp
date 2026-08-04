import { Ref } from "vue";
import { validateLead } from "../../../validators/lead";
import { LeadRequest, LeadResponse } from "../../../types/lead";
import { createStageLead } from "../../../api/lead";

const validate = (data: LeadRequest, isNew: boolean, errors: Ref<Record<string, string>>): boolean => {
    const validationErrors = validateLead(data, isNew);
    if (!validationErrors.isValid) {
        errors.value = validationErrors.errors;
        return false;
    }
    return true;
}

export function useLeadForm() {

    async function createLead(data: LeadRequest, isNew: boolean, errors: Ref<Record<string, string>>, generalError: Ref<string | null>): Promise<LeadResponse | null> {
        errors.value = {};
        if(!validate(data, isNew, errors))
            return null;
        try {
            return await createStageLead(data);
        }
        catch {
            generalError.value = 'Ошибка. Не удалось создать сделку! Попробуйте чуть позже';
            return null;
        }
    }
    
    return {
        createLead
    };
}