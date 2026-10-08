import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import './styles.css';
import { mountModule } from '../shared/mountModule';

mountModule('accounting-app', () => {
    void router.replace(location.pathname + location.search + location.hash);
    return createApp(App).use(createPinia()).use(router);
});
