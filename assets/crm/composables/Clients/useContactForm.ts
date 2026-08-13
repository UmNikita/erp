import { Ref } from "vue";
import { validateContact } from "../../validators/contact";
import { Contact, ContactRequest } from "../../types/contact";
import { createContact as createContactApi, updateContact as updateContactApi } from "../../api/contacts";

const validate = (data: ContactRequest, errors: Ref<Record<string, string>>): boolean => {
    const validationErrors = validateContact(data);
    if (!validationErrors.isValid) {
        errors.value = validationErrors.errors;
        return false;
    }
    return true;
}

export function useContactForm() {

    async function createContact(data: ContactRequest, errors: Ref<Record<string, string>>, generalError: Ref<string | null>): Promise<Contact | null> {
        errors.value = {};
        if(!validate(data, errors))
            return null;
        
        try {
            const contact = await createContactApi(data);
            return contact;
        }
        catch {
            generalError.value = 'Не удалось создать контакт. Попробуйте чуть позже';
            return null;
        }
    }

    async function updateContact(oldData: Contact, data: ContactRequest, errors: Ref<Record<string, string>>, generalError: Ref<string | null>): Promise<Contact | null> {
        errors.value = {};
        console.log(data);
        if(!validate(data, errors))
            return null;
        
        try {
            const obj = {};
            for (const key of Object.keys(data) as (keyof ContactRequest)[]) {
                if (data[key] !== oldData[key]) {
                    obj[key] = data[key];
                }
            }
            if(Object.keys(obj).length == 0){
                generalError.value = 'Измените данные!';
                return null;
            }
            
            const contact = await updateContactApi(obj, oldData.id);
            return contact;
        }
        catch {
            generalError.value = 'Не удалось обновить контакт. Попробуйте чуть позже';
            return null;
        }
    }
    
    return {
        createContact, updateContact
    };
}