<template>
    <div class="activity-item">
        <div class="activity-avatar">
            <svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="8" r="4"/><path d="M5 20C5.4 15.8 7.8 14 12 14C16.2 14 18.6 15.8 19 20H5Z"/></svg>
        </div>

        <div class="activity-item__body">
            <div class="activity-item__header">
            <span class="activity-item__name">{{ message.userName }}</span>
            <span class="activity-item__time">{{ message.date }}</span>
            </div>

            <div class="activity-message activity-message--green">
              {{ message.message }}
            </div>
        </div>
        </div>
</template>

<script setup lang="ts">
  import { Message } from '../../../types/lead.ts';

  const props = defineProps<{
    message: Message;
  }>();

</script>

<style scoped>
.activity-item {
    display: grid;
    grid-template-columns: 31px minmax(0, 1fr);
    gap: 8px;
    margin-bottom: 13px;
  }
  
.activity-avatar {
    width: 31px;
    height: 31px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #7a8496;
    background: #e9edf2;
    border-radius: 50%;
  }

  .activity-avatar svg {
    width: 18px;
    height: 18px;
  }

  .activity-item__body {
    min-width: 0;
  }

  .activity-item__header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 6px;
  }

  .activity-item__name {
    color: #254675;
    font-size: 10.5px;
    font-weight: 700;
  }

  .activity-item__time {
    color: #7f899a;
    font-size: 9px;
  }

  .activity-message {
    padding: 10px 11px;
    color: #273246;
    background: #eef2f7;
    border-radius: 8px;
    font-size: 10px;
    line-height: 1.45;
  }

  .activity-message--green {
    background: #eaf6ef;
  }

  .activity-message--blue {
    background: #edf5fd;
  }
</style>