import { LeadErrors, LeadRequest } from '../types/lead';
import { email, max, phone, required, validate } from './rules';
import { ValidationResult } from './validationResult';

const MAX_LENGTH_STR = 50
const MAX_BUDGET = 1000000
const MAX_COMMENT_STR = 255

export function validateLead(data: LeadRequest, isNewClient: boolean): ValidationResult<LeadErrors> {
    const errors: LeadErrors = {
        name: null,
        budget: null,
        product: null,
        source: null,
        next_action: null,
        comment: null,
        stage_id: null,
        client_id: null,
        new_client: null,
        responsible_id: null
    };
    const name = data.name?.trim();
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
        required("Необходимо выбрать этап")
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

    return {isValid: !Object.values(errors).some(error => error !== null), errors};
}

export function validateLeadEdit(data: LeadRequest): ValidationResult<LeadErrors> {
    const errors: LeadErrors = {
        name: null,
        budget: null,
        product: null,
        source: null,
        next_action: null,
        comment: null,
        stage_id: null,
        client_id: null,
        new_client: null,
        responsible_id: null
    };
    
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

    return {isValid: !Object.values(errors).some(error => error !== null), errors};
}

export function validateLeadName(value: string): ValidationResult<{name: string | null}> {
    const name = value.trim();
    const errors: {name: string | null} = {name: null};
    validate(errors, 'name', [
        required("Название обязательно"),
        max(`Название должно быть не болше ${MAX_LENGTH_STR} символов`, MAX_LENGTH_STR)
    ], name);

    return {isValid: !Object.values(errors).some(error => error !== null), errors};
}