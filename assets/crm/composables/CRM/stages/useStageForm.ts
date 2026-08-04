import { Ref } from "vue";
import { createStage as createStageApi } from "../../../api/stage";
import { StageRequest, StageResponse } from "../../../types/stage";
import { validateStage } from "../../../validators/stage";

const validate = (data: StageRequest, errors: Ref<Record<string, string>>): boolean => {
    const validationErrors = validateStage(data);
    if (!validationErrors.isValid) {
        errors.value = validationErrors.errors;
        return false;
    }
    return true;
}

export function useStageForm() {

    async function createStage(data: StageRequest, errors: Ref<Record<string, string>>, generalError: Ref<string | null>): Promise<StageResponse | null> {
        errors.value = {};
        if(data.pipeline_id == null) {
            generalError.value = "Ошибка. Перезагрузите страницу!";
            return null;
        }
        if(!validate(data, errors))
            return null;
        try {
            return await createStageApi(data);
        }
        catch {
            generalError.value = 'Ошибка. Не удалось создать этап!';
            return null;
        }
    }
    
    return {
        createStage
    };
}