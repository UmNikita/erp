<template>
    <header class="topbar">
        <label class="search">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <circle cx="11" cy="11" r="7.5" stroke="currentColor" stroke-width="1.8"/>
              <path d="M16.5 16.5L21 21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            <input id="searchInput" type="search" placeholder="Поиск по сделкам, клиентам, компаниям..." />
        </label>

        <button class="create-btn" type="button" @click="openModal(MODALS.CREATE_LEAD)" >
            <PlusIco />
            Создать сделку
        </button>
    </header>
</template>

<script setup lang="ts">
  import { MODALS, useModal } from '../../../../composables/useModal.ts';
  import PlusIco from '../../../icons/PlusIco.vue';

  const {openModal} = useModal();
</script>

<style>
    .topbar {
      min-height: 94px;
      display: flex;
      align-items: center;
      gap: 24px;
      padding: 16px 26px;
      border-bottom: 1px solid var(--line);
    }

    .search {
      position: relative;
      flex: 0 1 660px;
      border: none;
    }

    .search svg {
      position: absolute;
      left: 24px;
      top: 50%;
      width: 25px;
      height: 25px;
      transform: translateY(-50%);
      color: #778195;
      pointer-events: none;
    }

    .search input {
      width: 100%;
      height: 62px;
      padding: 0 24px 0 70px;
      border: 1px solid var(--line);
      border-radius: 13px;
      outline: none;
      background: #fff;
      color: var(--text);
      font-size: 19px;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .search input::placeholder {
      color: #8a92a2;
    }

    .create-btn {
      height: 58px;
      margin-left: auto;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 14px;
      padding: 0 27px;
      border: 0;
      border-radius: 13px;
      background: linear-gradient(180deg, #136dff 0%, #065ce9 100%);
      color: #fff;
      font-size: 18px;
      font-weight: 500;
      box-shadow: 0 7px 16px rgba(11, 99, 246, 0.22);
      transition: transform 0.16s, box-shadow 0.16s, background 0.16s;
      white-space: nowrap;
    }

    .create-btn svg {
      width: 22px;
      height: 22px;
    }

    @media (max-width: 980px) {
      .topbar {
        min-height: auto;
        flex-wrap: wrap;
        padding: 14px;
      }
    }
</style>