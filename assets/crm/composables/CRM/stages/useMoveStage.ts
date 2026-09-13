import { moveStage } from '../../../api/stage.ts';
import { useKanbanStore } from '../../../stores/kanban.ts';
import { StageUI } from '../../../types/stage.ts';

export function useMoveStage() {

    const kanbanStore = useKanbanStore();

    async function moveForwardStage(stage: StageUI) {
        const seq = uiForwardStage(stage);
        if(seq) {
            try {
                await moveStage(stage.id, seq);
            }
            catch {
                alert("Не удалось переместить этап! Возникла ошибка!")
                uiBackStage(stage);
            }
        } 
    }

    async function moveBackStage(stage: StageUI) {
        const seq = uiBackStage(stage);
        if(seq) {
            try {
                await moveStage(stage.id, seq);
            }
            catch {
                alert("Не удалось переместить этап! Возникла ошибка!")
                uiForwardStage(stage);
            }
        } 
    }

    function uiForwardStage(stage: StageUI) {
        if (!kanbanStore.kanban)
            return;

        const stages = kanbanStore.kanban.stages;
        const index = stages.findIndex(s => s.id === stage.id);

        if (index === -1 || index === stages.length - 1)
            return;

            const current = stages[index];
            const next = stages[index + 1];

            const sequence = current.sequence;
            current.sequence = next.sequence;
            next.sequence = sequence;

        [stages[index], stages[index + 1]] = [stages[index + 1], stages[index]];

        return sequence + 1;
    }
    
    function uiBackStage(stage: StageUI) {
        if (!kanbanStore.kanban)
            return;

        const stages = kanbanStore.kanban.stages;
        const index = stages.findIndex(s => s.id === stage.id);

        if (index <= 0)
            return;

        const current = stages[index];
        const previous = stages[index - 1];

        const sequence = current.sequence;
        current.sequence = previous.sequence;
        previous.sequence = sequence;

        [stages[index - 1], stages[index]] = [stages[index], stages[index - 1]];

        return sequence - 1;
    }

    return {
        moveForwardStage,
        moveBackStage
    };
}