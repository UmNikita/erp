
<template>
    <section class="deal-activity">
        <div class="activity-feed">
          <DayMessages :lead-message="message" v-for="message in leadMessages" />
        </div>

        <div class="activity-compose">
          <button class="activity-compose__attach" type="button">
            <svg viewBox="0 0 24 24" fill="none"><path d="M9 12.5L14.7 6.8A3 3 0 1 1 19 11L11 19A5 5 0 0 1 4 12L12 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
          </button>

          <input type="text" placeholder="Написать сообщение..." v-model="message">

          <button class="activity-compose__send" type="button" @click="sendMessage">
            <svg viewBox="0 0 24 24" fill="none"><path d="M21 3L10 14M21 3L14 21L10 14L3 10L21 3Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>
      </section>
</template>

<script setup lang="ts">
  import { LeadDetail, Message } from '../../../types/lead.ts';
  import DayMessages from './DayMessages.vue';
  import { sendLeadMessage } from '../../../api/lead.ts';
  import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';

  const props = defineProps<{
      lead: LeadDetail;
      leadMessages: Message[];
  }>();

  const message = defineModel<string>();
  const leadMessages = ref<Record<string, Message[]>>();

  const router = useRouter();

  async function sendMessage() {
    if(message.value?.length == 0 || !message.value)
      return;
    try {
      const lead = await sendLeadMessage(message.value, props.lead.id);
      message.value = '';
    }
    catch {
      alert("Произошла ошибка отправки!");
      return;
    }
    router.go(0);
  }

  onMounted(()=> {
    leadMessages.value = getGroupMessages();
  });

function getGroupMessages() {
  const groups = props.leadMessages.reduce((groups, item) => {
    const day = item.date.split('T')[0];

    if (!groups[day]) {
      groups[day] = [];
    }

    groups[day].push(item);

    return groups;
  }, {} as Record<string, Message[]>);

  Object.values(groups).forEach(messages => {
    messages.sort(
      (a, b) => new Date(a.date).getTime() - new Date(b.date).getTime()
    );
  });

  return Object.fromEntries(
    Object.entries(groups).sort(([a], [b]) => a.localeCompare(b))
  );
}
</script>

<style scoped>

  .deal-activity {
    background: #fff;
    border: 1px solid #e4e8ee;
    border-radius: 13px;
    box-shadow: 0 2px 8px rgba(28,39,56,.025);
    min-height: 450px;
    display: flex;
    flex-direction: column;
    padding: 13px;
  }

  .activity-feed {
    padding: 3px 7px 12px;
    height: 500px;
    overflow: auto;
  }

  .activity-compose {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px;
    background: #fff;
    border: 1px solid #d4dae3;
    border-radius: 8px;
  }

  .activity-compose__attach {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    color: #718097;
    background: transparent;
    border: 0;
  }

  .activity-compose__attach svg {
    width: 17px;
    height: 17px;
  }

  .activity-compose input {
    min-width: 0;
    flex: 1;
    height: 34px;
    padding: 0 5px;
    color: #273246;
    background: transparent;
    border: 0;
    outline: none;
    font-size: 11px;
  }

  .activity-compose input::placeholder {
    color: #8a94a5;
  }

  .activity-compose__send {
    width: 42px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    color: #fff;
    background: #0878f9;
    border: 0;
    border-radius: 7px;
  }

  .activity-compose__send:hover {
    background: #066ce1;
  }

  .activity-compose__send svg {
    width: 19px;
    height: 19px;
  }
</style>