<template>
    <p>Итого по воронке: {{ kanban.leadsCount }} {{getPluralizeLead(kanban.leadsCount)}} | {{ kanban.moneyAmount }} ₽</p>
</template>

<script setup lang="ts">
    import { Kanban } from '../../types/kanban';
    import { getPluralizeLead } from '../../utils/words';

    defineProps<{kanban: Kanban}>();
</script>