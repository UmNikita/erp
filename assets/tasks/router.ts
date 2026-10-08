import { createRouter, createWebHistory } from 'vue-router';
import WorkspaceView from './views/WorkspaceView.vue';
import TableView from './views/TableView.vue';
import DutiesView from './views/DutiesView.vue';
import DutyDetailView from './views/DutyDetailView.vue';
import ProfileView from './views/ProfileView.vue';
import AdminView from './views/AdminView.vue';

export default createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/tasks', component: WorkspaceView },
        { path: '/tasks/table', component: TableView },
        { path: '/tasks/duties', component: DutiesView },
        { path: '/tasks/duties/new', component: DutyDetailView },
        { path: '/tasks/duties/:id', component: DutyDetailView },
        { path: '/tasks/profile', component: ProfileView },
        { path: '/tasks/admin', component: AdminView },
        { path: '/tasks/:pathMatch(.*)*', redirect: '/tasks' },
    ],
});
