<template>
    <div>
       <label>{{ titleField }}</label>
        <div v-if="!editing">{{ valueField !== null && valueField !== undefined ? valueField : '-' }}</div>
        <input v-else v-model.number="value" />
        <p class="err" v-if="error">{{ error }}</p> 
    </div>
    
</template>

<script setup lang="ts">

    const value = defineModel();

    const props = defineProps<{
        titleField: string;
        valueField: string | number | null;
        editing: boolean;
        error?: string;
    }>();
</script>