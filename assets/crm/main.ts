import { createApp } from 'vue'
import App from './App.vue'
import { createRouter, createWebHistory } from 'vue-router';
import KanbanView from './views/KanbanView.vue';
import ClientsView from './views/ClientsView.vue';
import RobotsView from './views/RobotsView.vue';
import IntegrationsView from './views/IntegrationsView.vue';
import ArchiveView from './views/ArchiveView.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/crm',
            component: KanbanView
        },
        {
            path: '/crm/clients',
            component: ClientsView
        },
        {
            path: '/crm/robots',
            component: RobotsView
        },
        {
            path: '/crm/integrations',
            component: IntegrationsView
        },
        {
            path: '/crm/archive',
            component: ArchiveView
        }
    ]
});

createApp(App).use(router).mount('#crm-app');