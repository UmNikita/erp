<template>
    <div class="activity-date">{{ leadMessage[0].date }}</div>
    <Message :message="message" v-for="message in leadMessage" />
</template>

<script setup lang="ts">
    import { Message as MessageChat } from '../../../types/lead.ts';
    import Message from './Message.vue';

    const props = defineProps<{
        leadMessage: Record<string, MessageChat[]>[];
    }>();

    console.log(props.leadMessage)

</script>

<style scoped>
    .activity-date {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 4px 0 12px;
        color: #69758a;
        font-size: 10px;
        text-align: center;
    }

    .activity-date::before,
    .activity-date::after {
        content: "";
        height: 1px;
        flex: 1;
        background: #dfe4ea;
    }
</style>