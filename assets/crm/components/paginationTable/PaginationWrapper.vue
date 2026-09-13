<template>
    <section>
        <slot />
        <TableFooter v-if="init"
            :page="page" :range="range" :is-start="isStart()" :is-end="isEnd()" 
            :count="totalRecords" :client-count="allCount"
        />
    </section>
</template>

<script setup lang="ts">
    import { computed, watch } from 'vue';
    import TableFooter from './TableFooter.vue';
    import { useRoute } from 'vue-router';
    import { usePagination } from '../../composables/usePagination.ts';

    const route = useRoute();
    const page = computed(() => Number(route.query.page ?? 1));
    const {allCount, isStart, isEnd, range, setStates, totalRecords, init} = usePagination(page);
    const emit = defineEmits(['setPage', 'btnClick']);

    const props = defineProps<{
        btnTitle: string;
        title: string;
        subTitle: string;
    }>();
    
    watch(
        page,
        async (newPage) => {
            emit('setPage', newPage, (allCount: number)=> setStates(allCount));
        },
        { immediate: true }
    );
</script>