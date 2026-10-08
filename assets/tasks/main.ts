import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import './styles.css';
import './admin-more.css';
import './ui-update.css';
import { mountModule } from '../shared/mountModule';

mountModule('tasks-app', () => {
    void router.replace(location.pathname + location.search + location.hash);
    return createApp(App).use(createPinia()).use(router);
});
