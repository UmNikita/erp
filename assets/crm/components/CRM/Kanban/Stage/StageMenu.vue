<template>
    <div class="column-header-actions">
        <button class="column-menu-button" @click="isOpen = !isOpen">⋮</button>
        <div class="stage-menu" v-if="isOpen">
            <button @click="rename">Переименовать</button>
            <button class="danger" @click="remove">Удалить</button>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { ref } from 'vue';
    const isOpen = ref<boolean>(false);

    const emit = defineEmits<{
        rename: [];
        delete: [];
    }>();

    function rename() {
        isOpen.value = false;
        emit('rename');
    }
    function remove() {
        isOpen.value = false;
        emit('delete');
    }
    </script>

<style>
    .column-header-actions {
        position: relative;
    }

    .column-menu-button {
        background: none;
        border: none;
        color: rgb(63, 63, 63);
        cursor: pointer;
        font-size: 20px;
        line-height: 1;
        padding: 4px 8px;
        border-radius: 6px;
    }

    .column-menu-button:hover {
        background: var(--background-hover);
    }


    .stage-menu {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;

        width: 180px;
        padding: 6px;

        background: white;
        border: 1px solid var(--line);
        border-radius: 10px;

        box-shadow: 0 8px 20px rgba(0, 0, 0, .12);

        z-index: 20;
    }


    .stage-menu button {
        width: 100%;
        display: flex;
        align-items: center;

        padding: 10px 12px;

        border: none;
        background: none;

        border-radius: 6px;

        text-align: left;
        font-size: 14px;

        cursor: pointer;
    }


    .stage-menu button:hover {
        background: #f3f4f6;
    }


    .stage-menu .danger {
        color: #dc2626;
    }


    .stage-menu .danger:hover {
        background: #fef2f2;
    }
</style>