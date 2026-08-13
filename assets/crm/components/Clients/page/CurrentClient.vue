<template>
    <template v-if="loading">
        <section class="shell" v-if="!error">
            
            <HeaderClient />

            <nav class="tabs">
                <router-link class="tab" :class="{ active: routeName == 'client-review' }" :to="clientUrl(clientId)">Клиент</router-link>
                <router-link class="tab" :class="{ active: routeName == 'client-emails' }" :to="clientEmailUrl(clientId)">Почта</router-link>
                <router-link class="tab" :class="{ active: routeName == 'client-leads' }" :to="clientLeadsUrl(clientId)">Сделки</router-link>
                <router-link class="tab" :class="{ active: routeName == 'client-contacts' }" :to="clientContactsUrl(clientId)">Контакты</router-link>
                <router-link class="tab" :class="{ active: routeName == 'client-story' }" :to="clientStoryUrl(clientId)">История</router-link>
            </nav>

            <RouterView />

        </section>
        <Error v-else />
    </template>
</template>

<script setup lang="ts">

    import { useRoute } from 'vue-router';
    import HeaderClient from './HeaderClient.vue';
    import { clientContactsUrl, clientEmailUrl, clientLeadsUrl, clientStoryUrl, clientUrl } from '../../../routes/client.ts';
    import { computed, onMounted, ref } from 'vue';
    import Error from "../../Error.vue";
    import { useCurrentClient } from '../../../composables/Clients/useCurrentClient.ts';

    const error = ref(false);
    const loading = ref(false);
    
    const { load } = useCurrentClient();

    const route = useRoute();
    const clientId = Number(route.params.id);
    const routeName = computed(() => route.name);

    onMounted(async ()=>{
        try{
            await load(clientId);
        }
        catch {
            error.value = true;
        }
        finally {
            loading.value = true;
        }
    })

</script>

<style scoped>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: var(--bg);
    color: var(--text);
    font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
}

button,
input,
select,
textarea {
    font: inherit;
}

button {
    cursor: pointer;
}


.page {
    min-height: 100vh;
    padding: 22px;
}

.shell {
    min-height: calc(100vh - 44px);
    padding: 28px;
    background: #fff;
    border: 1px solid #e2e6ec;
    border-radius: 16px;
    box-shadow: 0 5px 18px rgba(27, 39, 60, .06);
}

.btn {
    height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    padding: 0 16px;
    border-radius: 9px;
    font-weight: 600;
}

.btn svg {
    width: 18px;
}

.tabs {
    display: flex;
    border-bottom: 1px solid #e2e6ec;
    margin-bottom: 24px;
    overflow: auto;
}

.tab {
    height: 30px;
    padding: 0 18px;
    border: 0;
    background: transparent;
    color: #5f697c;
    white-space: nowrap;
}

.tab.active {
    color: #1267f4;
    font-weight: 650;
}
</style>