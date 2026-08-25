import { Ref } from "vue";
import { validateLead } from "../../../validators/lead";
import { LeadRequest, LeadResponse, LeadDetail } from "../../../types/lead";
import { createStageLead, updateLead as updateLeadApi } from "../../../api/lead";

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

    async function updateLead(oldData: LeadDetail, data: LeadRequest, errors: Ref<Record<string, string>>, generalError: Ref<string | null>): Promise<Client | null> {
        errors.value = {};
        if(!validate(data, false, errors))
            return null;
        try {
            const obj = {};
            for (const key of Object.keys(data) as (keyof LeadRequest)[]) {
                if (data[key] !== oldData[key]) {
                    obj[key] = data[key];
                }
            }
            if(Object.keys(obj).length == 0){
                generalError.value = 'Измените данные!';
                return null;
            }
            const client = await updateLeadApi(oldData.id, obj);
            return client;
        }
        catch {
            generalError.value = 'Не удалось обновить клиента. Попробуйте чуть позже';
            return null;
        }
    }
    
    
    return {
        createLead,
        updateLead
    };
}