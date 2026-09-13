import { computed, Ref, ref } from 'vue';

const LIMIT = 10;

export function usePagination(page: Ref<number>) {

    const allCount = ref(1);
    const range = computed(() => getPages(page.value));
    const totalPages = ref(1);
    const totalRecords = ref();
    const init = ref(false);

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
        totalPages.value = Math.ceil(count / LIMIT);
        if(allCount.value < LIMIT)
            totalRecords.value = allCount.value;
        else {
            if(page.value > allCount.value/10)
                totalRecords.value = allCount.value;
            else
                totalRecords.value = page.value * LIMIT;
        }
        init.value = true;
    }

    return {
        setStates,
        isStart,
        isEnd,
        allCount,
        range,
        totalRecords,
        init
    };
}