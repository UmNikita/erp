<template>
    <section class="shell">
        <slot />
        <TableFooter v-if="init"
            :page="page" :range="range" :is-start="isStart()" :is-end="isEnd()" 
            :count="totalRecords" :client-count="allCount" @to="emit('to', $event)"
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
    const emit = defineEmits(['setPage', 'to']);
    
    watch(
        page,
        async (newPage) => {
            emit('setPage', newPage, (allCount: number)=> setStates(allCount));
        },
        { immediate: true }
    );
</script>

<style scoped>

    .shell {
        min-height: calc(100vh - 44px);
        padding: 28px;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 16px;
        box-shadow: 0 5px 18px rgba(27, 39, 60, .06);
    }

    @media(max-width:850px){
        .shell{
            min-height:100vh;
            padding:20px 14px;
            border-radius:0
        }
    }
</style>