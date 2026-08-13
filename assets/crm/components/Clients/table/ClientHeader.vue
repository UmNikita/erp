<template>
    <header class="header">
        <div>
            <h1>Клиенты</h1>
            <p>Контакты, сделки и история взаимодействия с клиентами</p>
        </div>
        <button class="primary" @click="openModal(MODALS.CREATE_CLIENT)">
            <PlusIco />
            Добавить клиента
        </button>
    </header>

    <div class="toolbar">
        <label class="search">
            <svg viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="m16.2 16.2 4.8 4.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <input placeholder="Поиск по имени, компании, телефону или email">
        </label>
        <div class="filters">
            <button class="filter">Фильтры</button>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { MODALS, useModal } from "../../../composables/useModal.ts";
    import PlusIco from "../../icons/PlusIco.vue"

     const { openModal } = useModal();
</script>

<style scoped>
    .header {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        margin-bottom: 26px;
    }

    .header h1 {
        margin: 0 0 7px;
        font-size: 29px;
    }

    .header p {
        margin: 0;
        color: var(--muted);
        font-size: 14px;
    }

    .primary {
        height: 48px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 0 20px;
        border: 0;
        border-radius: 10px;
        background: #1267f4;
        color: #fff;
        font-weight: 600;
        box-shadow: 0 7px 16px rgba(18, 103, 244, .2);
        cursor: pointer;
    }

    .primary svg {
        width: 20px;
    }

    .toolbar {
        display: grid;
        grid-template-columns: minmax(300px, 1fr) auto;
        gap: 14px;
        margin-bottom: 18px;
    }

    .search {
        position: relative;
    }

    .search svg {
        position: absolute;
        left: 16px;
        top: 50%;
        width: 20px;
        transform: translateY(-50%);
        color: #7b8497;
    }

    .search input {
        width: 100%;
        height: 46px;
        padding: 0 16px 0 48px;
        border: 1px solid #d6dce5;
        border-radius: 10px;
        outline: 0;
    }

    .search input:focus {
        border-color: #1267f4;
        box-shadow: 0 0 0 3px rgba(18, 103, 244, .08);
    }

    .filters {
        display: flex;
        gap: 10px;
    }

    .filter {
        height: 46px;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0 14px;
        border: 1px solid #d6dce5;
        border-radius: 10px;
        background: #fff;
        color: #343b48;
        cursor: pointer;
    }

    .filter svg {
        width: 18px;
    }


    @media(max-width:850px) {
        .header, .toolbar {
            grid-template-columns:1fr;
            flex-direction:column
        }
        .primary{
            width:100%;
            justify-content:center
        }
        .filters{
            overflow:auto
        }
    }
</style>