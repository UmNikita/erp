<script setup lang="ts">
import { onMounted } from 'vue';
import AccountingSidebar from './components/AccountingSidebar.vue';
import { useAccountingStore } from './stores/accounting';

const store = useAccountingStore();
onMounted(() => store.load());
</script>

<template>
    <div class="accounting-layout">
        <AccountingSidebar />
        <main class="accounting-main">
            <div v-if="store.error" class="app-error">{{ store.error }}</div>
            <div v-if="store.loading && !store.operations.length" class="app-loader">Загружаем данные…</div>
            <router-view v-else />
        </main>
    </div>
</template>
