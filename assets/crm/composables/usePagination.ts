import { computed, Ref, ref } from 'vue';

const LIMIT = 10;

export function usePagination(page: Ref<number>) {

    const allCount = ref(1);
    const range = computed(() => getPages(page.value));
    const totalPages = ref(1);

    function getPages(currentPage: number, visible = 5): number[] {
        let start = Math.max(1, currentPage - Math.floor(visible / 2));
        let end = start + visible - 1;

        if (end > totalPages.value) {
            end = totalPages.value;
            start = Math.max(1, end - visible + 1);
        }

        return Array.from(
            { length: end - start + 1 },
            (_, i) => start + i
        );
    }

    function isEnd(): boolean {
        return page.value >= totalPages.value - 5;
    }

    function isStart(): boolean {
        return page.value <= 5;
    }

    function setStates(count: number) {
        allCount.value = count;
        if(allCount.value > LIMIT) {
            totalPages.value = Math.ceil(allCount.value / LIMIT);
        }
    }

    return {
        setStates,
        isStart,
        isEnd,
        allCount,
        range,
    };
}