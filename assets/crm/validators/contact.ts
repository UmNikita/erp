import { ContactRequest } from '../types/contact';
import { INN, phone as validatePhone, email as validateEmail, max, required, validate } from './rules';
import { ValidationResult } from './validationResult';

const MAX_LENGTH_NAME = 100
interface a {
    name: string;
    secondname: string;
    thirdname?: string;
    position?: string;
    phone?: string;
    email?: string;
    messenger?: string;
}
export function validateContact(data: ContactRequest): ValidationResult {
    const errors: Record<string, string> = {};
    const name = data.name?.trim();
    const secondname = data.secondname?.trim();
    const thirdname = data.thirdname?.trim();
    const position = data.position?.trim();
    const phone = data.phone?.trim();
    const email = data.email?.trim();
    const messenger = data.messenger?.trim();
    validate(errors, 'name', [
        required("Имя обязательно"),
        max(`Имя должно быть не больше ${MAX_LENGTH_NAME} символов`, MAX_LENGTH_NAME),
    ], name);
    validate(errors, 'secondname', [
        required("Фамилия обязательна"),
        max(`Фамилия должна быть не больше ${MAX_LENGTH_NAME} символов`, MAX_LENGTH_NAME),
    ], secondname);
    validate(errors, 'thirdname', [
        max(`Отчество должно быть не больше ${MAX_LENGTH_NAME} символов`, MAX_LENGTH_NAME),
    ], thirdname);
    validate(errors, 'position', [
        max(`Название позиции не должно быть больше ${MAX_LENGTH_NAME} символов`, MAX_LENGTH_NAME),
    ], position);
    validate(errors, 'phone', [
        validatePhone("Неправильный телефон")
    ], phone);
    validate(errors, 'email', [
        validateEmail("Неправильная почта"),
    ], email);
    validate(errors, 'messenger', [
        max(`Название мессенджера должно быть не больше ${MAX_LENGTH_NAME} символов`, MAX_LENGTH_NAME),
    ], messenger);
    return {isValid: Object.keys(errors).length === 0, errors};
}