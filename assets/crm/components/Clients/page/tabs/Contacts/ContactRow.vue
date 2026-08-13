<template>
    <tr>
        <td>{{ contact.name }}</td>
        <td>{{ contact.secondname }}</td>
        <td>{{ contact.thirdname ? contact.thirdname : '-' }}</td>
        <td>{{ contact.position ? contact.position : '-' }}</td>
        <td>{{ contact.phone ? formatPhone(contact.phone) : '-' }}</td>
        <td>{{ contact.email ? contact.email : '-' }}</td>
        <td>{{ contact.messenger ? contact.messenger : '-' }}</td>
        <td>
            <div class="actions">
                <button class="pipeline-settings__delete" @click="deleteContact">
                    <DeleteIco />
                </button>
                <button class="pipeline-settings__settings" @click="edit">
                    <SettingsIco />
                </button>
            </div>
        </td>
    </tr>
</template>

<script setup lang="ts">
    import { Contact } from "../../../../../types/contact.ts";
    import { formatPhone } from "../../../../../utils/fields.ts";
    import DeleteIco from "../../../../icons/DeleteIco.vue";
    import SettingsIco from "../../../../icons/SettingsIco.vue";

    const emit = defineEmits(['edit', 'delete']);

    function edit() {
        emit("edit", props.contact.id);
    }

    function deleteContact() {
        emit("delete", props.contact.id);
    }

    const props = defineProps<{
        contact: Contact;
    }>();

</script>

<style scoped>
    .table td {
        border-bottom: 1px solid #edf0f4;
        font-size: 13px;
    }

    .table tr {
        border-bottom: 1px solid #edf0f4;
    }

    .table tr:last-child td {
        border-bottom: 0;
    }

    .pipeline-settings__delete {
        width: 42px;
        height: 42px;
        color: #9a6266;
        flex: 0 0 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff4f4;
        border: 1px solid #f2d9db;
        border-radius: 9px;
        cursor: pointer;
        transition: 0.2s ease;
    }
    .pipeline-settings__settings {
        width: 42px;
        height: 42px;
        color: #667c7e;
        flex: 0 0 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #c5dfe1;
        border: 1px solid #a3c0c3;
        border-radius: 9px;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .pipeline-settings__settings:hover {
        color: #ffffff;
        background: #7cadb1;
        border-color: #7cadb1;
    }

    .pipeline-settings__delete:hover {
        color: #ffffff;
        background: #e5484d;
        border-color: #e5484d;
    }

    .pipeline-settings__delete svg {
        width: 20px;
        height: 20px;
    }
    
    .actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

</style>