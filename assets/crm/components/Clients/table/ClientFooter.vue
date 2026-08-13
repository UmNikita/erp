<template>
    <footer class="footer">
        <span>Показано {{ count }} из {{clientCount}} клиентов</span>
        <div class="pages">
            <router-link v-if="!isStart" class="page-btn" :to="clientTableUrl(page-1)"><</router-link>
            <div v-else class="page-btn deactive"><</div>
            <router-link class="page-btn" :class="{ active: page === num }" v-for="num in range" :to="clientTableUrl(num)">{{ num }}</router-link>
            <router-link v-if="!isEnd" class="page-btn" :to="clientTableUrl(page+1)">></router-link>
            <div v-else class="page-btn deactive">></div>
        </div>
    </footer>
</template>

<script setup lang="ts">
    import { clientTableUrl } from '../../../routes/client';

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