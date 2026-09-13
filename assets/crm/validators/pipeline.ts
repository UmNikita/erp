import { Pipeline, PipelineErrors } from '../types/pipeline';
import { max, required, validate } from './rules';
import { ValidationResult } from './validationResult';

const MAX_LENGTH_NAME = 50
const MAX_COUNT = 10

export function validatePipeline(name: string): ValidationResult<PipelineErrors> {
    const errors: PipelineErrors = {name: null};
    const value = name.trim();
    validate(errors, 'name', [
        required("Название обязательно"),
        max("Название должно быть не болше 50 символов", MAX_LENGTH_NAME),
    ], value);
    return {isValid: !Object.values(errors).some(error => error !== null), errors};
}

export function isMaxPipeline(pipelines: Pipeline[]): boolean {
    if(pipelines.length > MAX_COUNT || pipelines.length == MAX_COUNT)
        return true;
    return false;
}