import { Ref } from "vue";
import { validateKP } from "../../validators/client";
import { ClientDetail } from "../../types/client";
import { sendKP as sendKPApi } from "../../api/client"
import { KpRequest } from "../../types/client";

const validate = (data: KpRequest, errors: Ref<Record<string, string>>): boolean => {
    const validationErrors = validateKP(data);
    if (!validationErrors.isValid) {
        errors.value = validationErrors.errors;
        return false;
    }
    return true;
}

export function useEmailForm() {

    async function sendKP(currentClient: ClientDetail, data: KpRequest, errors: Ref<Record<string, string>>, generalError: Ref<string | null>): Promise<boolean | null> {
        errors.value = {};
        if(!validate(data, errors))
            return null;

        if(!currentClient.email && !data.contact_id) {
            generalError.value = "Выберете контакт или добавьте клиенту почту!";
            return null;
        }

        if(data.contact_id && !data.contact_email) {
            generalError.value = "У выбранного контакта нет поля!";
            return null;
        }
            
        try {
            await sendKPApi(currentClient.id, data);
            return true;
        }
        catch {
            generalError.value = 'Не удалось отправить письмо. Попробуйте чуть позже';
            return null;
        }
    }
    
    return {
        sendKP
    };
}