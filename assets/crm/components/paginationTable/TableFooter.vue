<template>
    <footer class="footer">
        <span>Показано {{ count }} из {{clientCount}}</span>
        <div class="pages">
            <button v-if="!isStart" class="page-btn" @click="emit('to', props.page-1)"><</button>
            <div v-else class="page-btn deactive"><</div>
            <button class="page-btn" :class="{ active: props.page === num }" v-for="num in range" @click="emit('to', num)">{{ num }}</button>
            <button v-if="!isEnd" class="page-btn" @click="emit('to', props.page+1)">></button>
            <div v-else class="page-btn deactive">></div>
        </div>
    </footer>
</template>

<script setup lang="ts">

    const emit = defineEmits(['to']);

    const props = defineProps<{
        clientCount: number;
        count: number;
        page: number;
        range: number[];
        isStart: boolean;
        isEnd: boolean;
    }>();
    
</script>

<style scoped>
.footer{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:18px;
    color:var(--muted);
    font-size:13px
}
.pages{
    display:flex;
    gap:7px
}
.page-btn {
    min-width:36px;
    height:36px;
    border:1px solid var(--line);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius:8px;
    background:#fff;
    color:#4e596c;
    cursor: pointer;
}
.page-btn.deactive {
    background-color: #f5f5f5;
    color: #c4c4c4;
}
.page-btn.active{
    background: #1267f4;
    color:#fff
}
@media(max-width:850px) {
    .footer{
        align-items:flex-start;
        flex-direction:column;
        gap:14px
    }
}
</style>