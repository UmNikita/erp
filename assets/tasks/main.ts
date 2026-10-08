import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import './styles.css';
import './admin-more.css';

createApp(App).use(createPinia()).use(router).mount('#tasks-app');
