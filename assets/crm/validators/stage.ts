import { StageErrors, StageRequest, StageUI } from '../types/stage';
import { required, validate, max } from './rules';
import { ValidationResult } from './validationResult';

const MAX_LENGTH_NAME = 50
const MAX_COUNT = 20

export function validateStage(data: StageRequest): ValidationResult<StageErrors> {
    const errors: StageErrors = {name: null, color: null};
    const name = data.name.trim();
    validate(errors, 'name', [
        required("Название обязательно"),
        max("Название должно быть не болше 50 символов", MAX_LENGTH_NAME),
    ], name);
    return {isValid: !Object.values(errors).some(error => error !== null), errors};
}

export function isMaxStages(stages: StageUI[]): boolean {
    if(stages.length > MAX_COUNT || stages.length == MAX_COUNT)
        return true;
    return false;
}