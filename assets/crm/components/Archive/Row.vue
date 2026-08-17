<template>
    <tr>
        <td>
            <div class="archive-page__deal">
                <div class="archive-page__deal-icon">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M5 7H19V20H5V7Z" stroke="currentColor" stroke-width="1.7"/>
                        <path d="M8 7V5A2 2 0 0 1 10 3H14A2 2 0 0 1 16 5V7" stroke="currentColor" stroke-width="1.7"/>
                    </svg>
                </div>

                <div>
                    <strong>{{ lead.name }}</strong>
                    <span>{{ lead.product }}</span>
                </div>
            </div>
        </td>

        <td>{{ lead.client?.name }}</td>

        <td>
            <span class="archive-page__pipeline">
                {{ lead.stage.pipeline.name }}
            </span>
        </td>

        <td>
            <strong class="archive-page__amount">
                {{ formatAmount(lead.budget) }} ₽
            </strong>
        </td>

        <td>
            <div class="archive-page__manager">
            <span class="archive-page__avatar">M</span>
                {{ lead.responsible?.name }}
            </div>
        </td>

        <td>
            <span class="archive-page__date">
                {{ formatStatus(lead.status) }}
            </span>
        </td>

        <td>
            <span class="archive-page__date">
                {{ formatResponseDate(lead.dateStart) }}
            </span>
        </td>

        <td>
            <div class="archive-page__actions">
                <button
                    class="archive-page__delete"
                    type="button"
                    title="Удалить навсегда"
                    @click="deleteLead"
                >
                    <DeleteIco />
                </button>
            </div>
        </td>
    </tr>
</template>

<script setup lang="ts">
    import { LeadResponse } from '../../types/lead';
    import DeleteIco from "../icons/DeleteIco.vue";
    import { formatAmount, formatResponseDate, formatStatus } from '../../utils/fields.ts'

    const props = defineProps<{
        lead: LeadResponse;
    }>();

    const emit = defineEmits(['delete', 'active']);

    function deleteLead() {
        if(confirm("Вы действительно хотите удалить?"))
            emit('delete', props.lead);
    }

</script>

<style scoped>
    .archive-page__table tbody tr:last-child td {
        border-bottom: none;
    }
    .archive-page__table tbody tr {
        transition: background 0.16s ease;
    }
    .archive-page__table tbody tr:hover {
        background: #fbfcfe;
    }

    .archive-page__table td {
        padding: 16px;
        color: #424b5b;
        border-bottom: 1px solid #edf0f4;
        font-size: 13px;
        vertical-align: middle;
    }

    .archive-page__deal {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .archive-page__deal-icon {
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #657085;
        background: #f1f3f6;
        border-radius: 9px;
    }

    .archive-page__deal-icon svg {
        width: 20px;
        height: 20px;
    }

    .archive-page__deal strong {
        display: block;
        margin-bottom: 4px;
        color: #171b24;
        font-size: 13px;
        font-weight: 650;
    }

    .archive-page__deal span {
        color: #7a8497;
        font-size: 11px;
    }

    .archive-page__pipeline {
        display: inline-flex;
        padding: 6px 9px;
        color: #526077;
        background: #f1f3f6;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 500;
    }

    .archive-page__amount {
        color: #303846;
        font-size: 13px;
    }

    .archive-page__manager {
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .archive-page__avatar {
        width: 29px;
        height: 29px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #556073;
        background: #f0f2f5;
        border-radius: 50%;
        font-size: 10px;
        font-weight: 700;
    }

    .archive-page__date {
        color: #697487;
        white-space: nowrap;
    }

    .archive-page__actions {
        display: flex;
        justify-content: flex-end;
        gap: 7px;
    }

    .archive-page__restore,
    .archive-page__delete {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.18s ease;
    }

    .archive-page__restore {
        color: #1267f4;
        background: #edf4ff;
        border: 1px solid #d5e4ff;
    }

    .archive-page__restore:hover {
        color: #ffffff;
        background: #1267f4;
        border-color: #1267f4;
    }

    .archive-page__delete {
        color: #a75d63;
        background: #fff4f4;
        border: 1px solid #f1d8da;
    }

    .archive-page__delete:hover {
        color: #ffffff;
        background: #e5484d;
        border-color: #e5484d;
    }

    .archive-page__restore svg,
    .archive-page__delete svg {
        width: 17px;
        height: 17px;
    }
</style>