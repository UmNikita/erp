<script setup lang="ts">
import { useRoute } from 'vue-router';

const route = useRoute();
const links = [
    { name: 'Рабочий стол', path: '/accounting' },
    { name: 'Рабочая таблица', path: '/accounting/table' },
    { name: 'Аудит', path: '/accounting/audit' },
];
</script>

<template>
    <aside class="side">
        <div class="brand">REKLAM <b>HUB</b></div>
        <div class="side-sub">корпоративная система</div>
        <div class="side-section nav">
            <p class="side-title">Главное</p>
            <router-link
                v-for="link in links"
                :key="link.path"
                :to="link.path"
                :class="{ active: route.path === link.path }"
            >{{ link.name }}</router-link>
        </div>
    </aside>
</template>
