import { createRouter, createWebHistory } from 'vue-router';
import WorkspaceView from './views/WorkspaceView.vue';
import WorkingTableView from './views/WorkingTableView.vue';
import AuditView from './views/AuditView.vue';

export default createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/accounting', name: 'accounting-workspace', component: WorkspaceView },
        { path: '/accounting/table', name: 'accounting-table', component: WorkingTableView },
        { path: '/accounting/audit', name: 'accounting-audit', component: AuditView },
    ],
});
