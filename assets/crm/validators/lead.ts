import { LeadRequest } from '../types/lead';
import { email, max, phone, required, validate } from './rules';
import { ValidationResult } from './validationResult';

const MAX_LENGTH_STR = 50
const MAX_BUDGET = 1000000
const MAX_COMMENT_STR = 255

export function validateLead(data: LeadRequest, isNewClient: boolean): ValidationResult {
    const errors: Record<string, string> = {};
    
    const name = data.name.trim();
    validate(errors, 'name', [
        required("Название обязательно"),
        max(`Название должно быть не болше ${MAX_LENGTH_STR} символов`, MAX_LENGTH_STR)
    ], name);
    
    const budget = data.budget;
    validate(errors, 'budget', [
        max(`Сумма не должна превышать ${MAX_BUDGET}`, MAX_BUDGET)
    ], budget);
    
    const product = data.product?.trim();
    validate(errors, 'product', [
        max(`Название должно быть не болше ${MAX_LENGTH_STR} символов`, MAX_LENGTH_STR)
    ], product);
    
    const source = data.source?.trim();
    validate(errors, 'source', [
        max(`Название источника должно быть не болше ${MAX_LENGTH_STR} символов`, MAX_LENGTH_STR)
    ], source);
    
    const next_action = data.next_action?.trim();
    validate(errors, 'next_action', [
        max(`Название должно быть не болше ${MAX_LENGTH_STR} символов`, MAX_LENGTH_STR)
    ], next_action);

    const comment = data.comment?.trim();
    validate(errors, 'comment', [
        max(`Комментарий должен быть не болше ${MAX_COMMENT_STR} символов`, MAX_COMMENT_STR)
    ], comment);

    const stage_id = data.stage_id;
    validate(errors, 'stage_id', [
        required("Не выбран этап воронки")
    ], stage_id);

    const client = data.client;
    if(isNewClient) {
        validate(errors, 'new_client', [
            required("Требуется ввести имя клиента")
        ], client?.name);
        validate(errors, 'new_client', [
            email("Некорректный email")
        ], client?.email);
        validate(errors, 'new_client', [
            phone("Некорректный телефон")
        ], client?.phone);
    }
    

    return {isValid: Object.keys(errors).length === 0, errors};
}