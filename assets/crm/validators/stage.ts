import { StageRequest } from '../types/stage';
import { required, validate, max } from './rules';
import { ValidationResult } from './validationResult';

const MAX_LENGTH_NAME = 50

export function validateStage(data: StageRequest): ValidationResult {
    const errors: Record<string, string> = {};
    const name = data.name.trim();
    validate(errors, 'name', [
        required("Название обязательно"),
        max("Название должно быть не болше 50 символов", MAX_LENGTH_NAME),
    ], name);
    return {isValid: Object.keys(errors).length === 0, errors};
}