<script setup lang="ts">
import { useRoute } from 'vue-router';
defineProps<{ links: { name: string; path: string }[]; label?: string }>();
const route = useRoute();
</script>
<template>
    <aside class="side hub-module-side">
        <div class="brand">REKLAM <b>HUB</b></div>
        <div class="side-sub">корпоративная система</div>
        <nav class="side-section nav" :aria-label="label ?? 'Разделы модуля'">
            <p class="side-title">Главное</p>
            <router-link v-for="link in links" :key="link.path" :to="link.path" :class="{ active: route.path === link.path }">{{ link.name }}</router-link>
        </nav>
    </aside>
</template>
<style scoped>
.hub-module-side { background:#fff; border-right:1px solid #e6e8ec; padding:22px 18px; overflow-y:auto; }
.brand { font-size:23px; font-weight:850; color:#14171f; }
.brand b { color:#16b8d9; }
.side-sub { font-size:11px; color:#6b7280; text-transform:uppercase; letter-spacing:.12em; margin-top:2px; }
.side-section { margin-top:28px; }
.side-title { font-size:11px; color:#9aa3af; text-transform:uppercase; letter-spacing:.1em; margin:0 0 12px; }
.nav a { display:block; padding:12px 14px; border-radius:12px; margin:5px 0; font-size:14px; font-weight:700; color:#6b7280; text-decoration:none; }
.nav a:hover { background:#f3f5f7; color:#14171f; }
.nav a.active { background:#e8f8fc; color:#0ea5c6; }
@media(max-width:760px) { .hub-module-side { padding:10px; overflow-x:auto; } .brand,.side-sub,.side-title { display:none; } .side-section { display:flex; gap:4px; margin:0; } .nav a { white-space:nowrap; padding:10px; margin:0; } }
</style>
