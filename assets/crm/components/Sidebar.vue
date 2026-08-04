<script setup lang="ts">
import { useRoute } from 'vue-router';

const route = useRoute();

const links = [
    {
        name: 'Воронки',
        path: '/crm'
    },
    {
        name: 'Клиенты',
        path: '/crm/clients'
    },
    {
        name: 'Архив',
        path: '/crm/archive'
    },
    {
        name: 'Интеграции',
        path: '/crm/integrations'
    },
    {
        name: 'Роботы',
        path: '/crm/robots'
    }
];

function isActive(path: string) {
    return route.path === path;
}
</script>


<template>
    <aside class="side">
        <div class="brand">REKLAM <b>HUB</b></div>
        <div class="side-sub">корпоративная система</div>
        <div class="side-section nav">
            <p class="side-title">Главное</p>
            <router-link v-for="link in links"
                :key="link.path" :to="link.path"
                :class="{ active: isActive(link.path) }">
                {{ link.name }}
            </router-link>
                    
        </div>
    </aside>
</template>