import type { PipelineRequest } from '../types/pipeline';
import { max, required, validate } from './rules';
import { ValidationResult } from './validationResult';

const MAX_LENGTH_NAME = 50

export function validatePipeline(data: PipelineRequest): ValidationResult {
    const errors: Record<string, string> = {};
    const name = data.name.trim();
    validate(errors, 'name', [
        required("Название обязательно"),
        max("Название должно быть не болше 50 символов", MAX_LENGTH_NAME),
    ], name);
    return {isValid: Object.keys(errors).length === 0, errors};
}