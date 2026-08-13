<template>
    <div>
       <label>{{ titleField }}</label>
        <div v-if="!editing">{{ valueField ? valueField : '-' }}</div>
        <input v-else v-model="value" />
        <p class="err" v-if="error">{{ error }}</p> 
    </div>
    
</template>

<script setup lang="ts">

    const value = defineModel();

    const props = defineProps<{
        titleField: string;
        valueField: string | null;
        editing: boolean;
        error?: string;
    }>();
</script>