import { createRouter, createWebHistory } from 'vue-router';
import KanbanView from './views/KanbanView.vue';
import ClientsView from './views/ClientsView.vue';
import IntegrationsView from './views/IntegrationsView.vue';
import ArchiveView from './views/ArchiveView.vue';
import CurrentClient from './components/Clients/page/CurrentClient.vue';
import ReviewClient from './components/Clients/page/tabs/Review/ReviewClient.vue';
import LeadsClient from './components/Clients/page/tabs/Leads/LeadsClient.vue';
import EmailsClient from './components/Clients/page/tabs/Emails/EmailsClient.vue';
import StoryClient from './components/Clients/page/tabs/Story/StoryClient.vue';
import ContactsClient from './components/Clients/page/tabs/Contacts/ContactsClient.vue';
import CurrentLead from './components/Leads/CurrentLead.vue';

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
            path: '/crm/lead/:id',
            component: CurrentLead
        },
        {
            path: '/crm/client/:id',
            component: CurrentClient,
            children: [
                {
                    path: '',
                    redirect: {
                        name: 'client-review'
                    }
                },
                {
                    path: 'review',
                    name: 'client-review',
                    component: ReviewClient
                },
                {
                    path: 'leads',
                    name: 'client-leads',
                    component: LeadsClient
                },
                {
                    path: 'emails',
                    name: 'client-emails',
                    component: EmailsClient
                },
                {
                    path: 'story',
                    name: 'client-story',
                    component: StoryClient
                },
                {
                    path: 'contacts',
                    name: 'client-contacts',
                    component: ContactsClient
                }
            ]
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

export default router;