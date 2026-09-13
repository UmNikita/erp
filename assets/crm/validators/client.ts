import { ClientErrors, ClientRequest, KPErrors, KpRequest } from '../types/client';
import { INN, phone as validatePhone, email as validateEmail, max, required, validate, url } from './rules';
import { ValidationResult } from './validationResult';

const MAX_LENGTH_NAME = 50

export function validateClient(data: ClientRequest): ValidationResult<ClientErrors> {
    const errors: ClientErrors = {
        name: null,
        inn: null,
        field_of_activity: null,
        website: null,
        phone: null,
        email: null,
        city: null,
        channel: null
    };
    const name = data.name?.trim();
    const inn = data.inn?.trim();
    const field_of_activity = data.field_of_activity?.trim();
    const website = data.website?.trim();
    const phone = data.phone?.trim();
    const email = data.email?.trim();
    const city = data.city?.trim();
    const channel = data.channel?.trim();
    validate(errors, 'name', [
        required("Название обязательно"),
        max("Название должно быть не болше 50 символов", MAX_LENGTH_NAME),
    ], name);
    validate(errors, 'inn', [
        INN("Неправильный ИНН")
    ], inn);
    validate(errors, 'field_of_activity', [
        max("Название должно быть не болше 50 символов", MAX_LENGTH_NAME),
    ], field_of_activity);
    validate(errors, 'website', [
        max("Вебсайт не должен быть болше 50 символов", MAX_LENGTH_NAME),
        url("Неправильная ссылка")
    ], website);
    validate(errors, 'phone', [
        validatePhone("Неправильный телефон")
    ], phone);
    validate(errors, 'email', [
        validateEmail("Неправильная почта"),
    ], email);
    validate(errors, 'city', [
        max("Название города должно быть не болше 50 символов", MAX_LENGTH_NAME),
    ], city);
    validate(errors, 'channel', [
        max("Название канала должно быть не болше 50 символов", MAX_LENGTH_NAME),
    ], channel);
    return {isValid: !Object.values(errors).some(error => error !== null), errors};
}

export function validateKP(data: KpRequest): ValidationResult<KPErrors> {
    const errors: KPErrors = {
        manager_name: null,
        manager_phone: null,
        contact_email: null,
        contact_id: null
    };
    const manager_name = data.manager_name?.trim();
    const manager_phone = data.manager_phone?.trim();
    validate(errors, 'manager_name', [
        required("Имя обязательно"),
        max("Имя должно быть не болше 50 символов", MAX_LENGTH_NAME),
    ], manager_name);
    validate(errors, 'manager_phone', [
        required("Телефон обязателен"),
        validatePhone("Неправильный телефон")
    ], manager_phone);
    return {isValid: !Object.values(errors).some(error => error !== null), errors};
}