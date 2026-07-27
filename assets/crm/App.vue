<script setup lang="ts">
import Sidebar from './components/Sidebar.vue';

</script>

<template>
    <Sidebar />
    <main class="main">
        <router-view />
    </main>
</template>