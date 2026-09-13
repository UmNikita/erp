<template>
    <template v-if="loading">
        <section v-if="!error">
            <div class="section-head">
                <div>
                    <h2>Письма клиенту</h2>
                    <p>Отправленные письма, черновики и ошибки доставки</p>
                </div>
                <div>
                    <!-- <button class="btn btn-primary open-mail">+ Написать письмо</button> -->
                    <button class="btn btn-primary open-mail" @click="openModal(MODALS.SEND_KP)">Отправить КП</button>
                </div>
            </div>
            <!-- <div class="mail-filters">
                <button class="mail-filter active">Все</button>
                <button class="mail-filter">Отправленные</button>
                <button class="mail-filter">Черновики</button>
                <button class="mail-filter">Ошибки</button>
            </div> -->
            <div class="mail-list">
                <Email v-for="email in historyEmails" :email="email" />
            </div>
        </section>
        <Error v-else />
        <KpModal v-if="activeModal === MODALS.SEND_KP" @close="closeModal" />
    </template>
</template>

<script setup lang="ts">
    import { onMounted, ref } from 'vue';
    import KpModal from '../../../modals/KpModal.vue';
    import { MODALS, useModal } from '../../../../../composables/useModal.ts';
    import { useCurrentClient } from '../../../../../composables/Clients/useCurrentClient.ts';
    import Email from './Email.vue';
    import { useRoute } from 'vue-router';
    import Error from '../../../../Error.vue';

    const {activeModal, closeModal, openModal} = useModal();

    const { historyEmails  } = useCurrentClient();

    const { loadEmails } = useCurrentClient();
    const error = ref(false);
    const loading = ref(false);

    const route = useRoute();
    const clientId = Number(route.params.id);

    onMounted(async ()=>{
        try{
            await loadEmails(clientId);
        }
        catch {
            error.value = true;
        }
        finally {
            loading.value = true;
        }
    });

</script>

<style scoped>

    .btn-primary {
        background: #1267f4;
        color: #fff;
        box-shadow: 0 6px 14px rgba(18, 103, 244, .18);
        margin-left: 15px;
    }
    
    .section-head {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .section-head h2 {
        margin: 0 0 5px;
        font-size: 20px;
    }

    .section-head p {
        margin: 0;
        color: var(--muted);
        font-size: 13px;
    }

    .mail-filters {
        display: flex;
        gap: 7px;
        margin-bottom: 18px;
        overflow: auto;
    }

    .mail-filter {
        height: 36px;
        padding: 0 13px;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #fff;
        color: #606b7e;
        font-size: 12px;
        font-weight: 600;
    }

    .mail-filter.active {
        color: var(--blue);
        background: var(--soft);
        border-color: #cddfff;
    }

    .mail-list {
        display: grid;
        gap: 10px;
    }
</style>