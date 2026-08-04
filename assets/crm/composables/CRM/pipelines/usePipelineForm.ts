import { Ref } from "vue";
import { validatePipeline } from "../../../validators/pipeline";
import { createPipeline as createPipelineApi, deletePipeline, updatePipeline } from "../../../api/pipeline";
import { Pipeline, PipelineBuffersDTO, PipelineRequest } from "../../../types/pipeline";
import { pipelineToRequest } from "../../../mappers/pipelineMapper";

const validate = (data: PipelineRequest, errors: Ref<Record<string, string>>): boolean => {
    const validationErrors = validatePipeline(data);
    if (!validationErrors.isValid) {
        errors.value = validationErrors.errors;
        return false;
    }
    return true;
}

function getRequests(data: PipelineBuffersDTO): Promise<unknown>[] {
    const requests: Promise<unknown>[] = [];

    data.delete.forEach(pipeline => {
        requests.push(deletePipeline(pipeline));
    });

    data.update.forEach(pipeline => {
        requests.push(updatePipeline(pipeline));
    });
    
    return requests;
}

export function usePipelineForm() {

    async function createPipeline(data: PipelineRequest, errors: Ref<Record<string, string>>, generalError: Ref<string | null>): Promise<Pipeline | null> {
        errors.value = {};
        if(!validate(data, errors))
            return null;
        
        try {
            return await createPipelineApi(data);
        }
        catch {
            generalError.value = 'Не удалось создать воронку. Попробуйте чуть позже';
            return null;
        }
    }

    async function updateAndDeletePipelines(data: PipelineBuffersDTO, errors: Ref<Record<string, string>>, generalError: Ref<string | null>): Promise<any | null> {
        errors.value = {};
        data.update.forEach(element => {
            const pipeline = pipelineToRequest(element);
            if(!validate(pipeline, errors))
                return null;
        });
        try {
            const requests = getRequests(data);
            return await Promise.all(requests);
        }
        catch {
            generalError.value = 'Не удалось обновить воронки. Попробуйте чуть позже';
            return null;
        }
    }
    
    return {
        createPipeline,
        updateAndDeletePipelines
    };
}