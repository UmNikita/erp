import { Ref } from "vue";
import { validateClient } from "../../validators/client";
import { Client, ClientDetail, ClientRequest } from "../../types/client";
import {createClient as createClientApi, updateClient as updateClientApi} from "../../api/client"

const validate = (data: ClientRequest, errors: Ref<Record<string, string>>): boolean => {
    const validationErrors = validateClient(data);
    if (!validationErrors.isValid) {
        errors.value = validationErrors.errors;
        return false;
    }
    return true;
}

export function useClientForm() {

    async function createClient(data: ClientRequest, errors: Ref<Record<string, string>>, generalError: Ref<string | null>): Promise<Client | null> {
        errors.value = {};
        if(!validate(data, errors))
            return null;
        
        try {
            const client = await createClientApi(data);
            client.leads_amount = 0;
            client.leads_count = 0;
            return client;
        }
        catch {
            generalError.value = 'Не удалось создать клиента. Попробуйте чуть позже';
            return null;
        }
    }

    async function updateClient(oldData: ClientDetail, data: ClientRequest, errors: Ref<Record<string, string>>, generalError: Ref<string | null>): Promise<Client | null> {
        errors.value = {};
        console.log(data);
        if(!validate(data, errors))
            return null;
        
        try {
            const obj = {};
            for (const key of Object.keys(data) as (keyof ClientRequest)[]) {
                if (data[key] !== oldData[key]) {
                    obj[key] = data[key];
                }
            }
            if(Object.keys(obj).length == 0){
                generalError.value = 'Измените данные!';
                return null;
            }
            const client = await updateClientApi(oldData.id, obj);
            return client;
        }
        catch {
            generalError.value = 'Не удалось обновить клиента. Попробуйте чуть позже';
            return null;
        }
    }
    
    return {
        createClient,
        updateClient
    };
}