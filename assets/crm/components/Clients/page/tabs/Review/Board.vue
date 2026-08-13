<template>
    <article v-if="client" class="card">
        <div class="card-head">
            <h2>Информация о клиенте</h2>
            <button v-if="!editing" @click="startEditing" class="link">Редактировать</button>
            <div v-else>
                <button class="link accept" @click="acceptEditing">Принять</button>
                <button class="link" @click="cancelEditing">Отменить</button>
            </div>
            
        </div>
        <div class="info-grid">
            <RenameField title-field="Имя компании" :value-field="client.name" 
                class="info" :editing="editing" :error="errors.name" v-model="data.name" />
            <RenameField title-field="ИНН компании" :value-field="client.inn" 
                class="info" :editing="editing" :error="errors.inn" v-model="data.inn" />
            <RenameField title-field="Сфера деятельности" :value-field="client.field_of_activity" 
                class="info" :editing="editing" :error="errors.field_of_activity" v-model="data.field_of_activity" />
            <RenameField title-field="Сайт компании" :value-field="client.website" 
                class="info" :editing="editing" :error="errors.website" v-model="data.website" />
            <RenameField title-field="Телефон компании" :value-field="client.phone" 
                class="info" :editing="editing" :error="errors.phone" v-model="data.phone" />
            <RenameField title-field="Email компании" :value-field="client.email" 
                class="info" :editing="editing" :error="errors.email" v-model="data.email" />
            <RenameField title-field="Город" :value-field="client.city" 
                class="info" :editing="editing" :error="errors.city" v-model="data.city" />
            <RenameField title-field="Канал коммуникации" :value-field="client.channel" 
                class="info" :editing="editing" :error="errors.channel" v-model="data.channel" />
        </div>
        <p class="general-err" v-if="generalError">{{generalError}}</p>
    </article>
</template>

<script setup lang="ts">
    import { useClientForm } from '../../../../../composables/Clients/useClientForm';
    import { useCurrentClient } from '../../../../../composables/Clients/useCurrentClient';
    import { useInlineRenameForm } from '../../../../../composables/useInlineRenameForm';
    import RenameField from '../../../../common/RenameField.vue';

    const { updateClient } = useClientForm();
    const { client } = useCurrentClient();

    const {editing, errors, data, generalError, 
        startEditing, cancelEditing, accept, setNewData} = useInlineRenameForm(client.value);

    async function acceptEditing() {
        const newData = accept();
        if(newData == null)
            return;
        if(!client.value)
            return;
        const res = await updateClient(client.value, data.value, errors, generalError);
        if(res) {
            editing.value = false;
            setNewData();
        }
    }


</script>

<style scoped>
    .general-err {
        margin: 16px 0 0;
        padding: 10px 13px 10px 38px;

        color: #c9363e;
        background: #fff5f5;
        border: 1px solid #f2c9cc;
        border-radius: 8px;

        font-size: 14px;
        font-weight: 500;
    }
    .card {
        padding: 22px;
        border: 1px solid var(--line);
        border-radius: 13px;
        background: #fff;
    }

    .card-head {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }

    .card h2 {
        margin: 0;
        font-size: 17px;
    }

    .link {
        padding: 0;
        border: 0;
        background: transparent;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    .accept {
        margin-right: 15px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px 28px;
    }

    :deep(.info label) {
        display: block;
        margin-bottom: 7px;
        font-size: 12px;
        font-weight: 600;
    }

    :deep(.info div) {
        color: #303744;
        font-size: 14px;
        font-weight: 550;
    }

    :deep(.info input) {
        border: none;
        outline: none;
        border-bottom: 1px solid #303744;
        color: #303744;
        width: 250px;
    }

    :deep(.err) {
        color: #d93d42;
        font-size: 12px;
        line-height: 1.4;
        font-weight: 500;
    }
</style>