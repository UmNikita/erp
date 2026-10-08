<script setup lang="ts">
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { useTaskStore } from '../store';
import TeamRanking from './TeamRanking.vue';
const props = defineProps<{ collapsed: boolean }>();
defineEmits<{ toggle: [] }>();
const store = useTaskStore();
const comments = computed(() => store.feedback.filter(item => item.recipient.id === store.currentUser?.id).sort((a, b) => Date.parse(b.createdAt) - Date.parse(a.createdAt) || b.id - a.id));
const unread = computed(() => comments.value.some(item => !item.readAt));
const scrollRoot = ref<HTMLElement | null>(null);
const reading = new Set<number>();
let observer: IntersectionObserver | null = null;
async function observeComments() {
    observer?.disconnect();
    await nextTick();
    if (props.collapsed || !scrollRoot.value || document.visibilityState !== 'visible') return;
    observer = new IntersectionObserver(entries => {
        for (const entry of entries) {
            const id = Number((entry.target as HTMLElement).dataset.feedbackId);
            if (!entry.isIntersecting || document.visibilityState !== 'visible' || props.collapsed || reading.has(id) || comments.value.find(item => item.id === id)?.readAt) continue;
            reading.add(id);
            void store.markFeedbackRead(id).finally(() => reading.delete(id));
        }
    }, { root: scrollRoot.value, threshold: 0.1 });
    scrollRoot.value.querySelectorAll('[data-feedback-id]').forEach(item => observer?.observe(item));
}
watch(() => [props.collapsed, ...comments.value.map(item => `${item.id}:${item.readAt}`)], observeComments, { flush: 'post' });
onMounted(() => { void observeComments(); document.addEventListener('visibilitychange', observeComments); });
onUnmounted(() => { observer?.disconnect(); document.removeEventListener('visibilitychange', observeComments); });
const date = (value: string) => new Intl.DateTimeFormat('ru-RU', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' }).format(new Date(value));
</script>
<template>
    <aside class="task-manager-menu" :class="{ collapsed, 'has-unread': collapsed && unread }" aria-label="Комментарии руководителя и рейтинг команды">
        <button class="task-manager-toggle" :aria-expanded="!collapsed" :aria-label="collapsed ? 'Показать комментарии' : 'Свернуть комментарии'" @click="$emit('toggle')">{{ collapsed ? '‹' : '›' }}</button>
        <header v-if="!collapsed"><strong>Комментарии<br />руководителя</strong></header>
        <span v-if="collapsed && unread" class="task-manager-unread" role="status" aria-label="Новый комментарий руководителя">●</span>
        <div v-if="!collapsed" ref="scrollRoot" class="task-manager-scroll"><section aria-label="Комментарии руководителя"><p v-if="!comments.length" class="task-muted">Здесь появятся комментарии руководителя к вашей работе.</p><article v-for="item in comments" :key="item.id" :data-feedback-id="item.id" class="task-feedback-entry"><div><strong>{{ item.author?.name ?? 'Руководитель' }}</strong><time>{{ date(item.createdAt) }}</time></div><p>{{ item.body }}</p></article></section></div>
        <div v-if="!collapsed" class="task-manager-ranking"><TeamRanking /></div>
    </aside>
</template>
