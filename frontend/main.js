import "@/framework/utils/extension.js"
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import loadUI from '@/framework/ui'
import '@/assets/css/main.css'

const app = createApp(App);
async function initApp() {
    await loadUI(app);
    app.use(createPinia()).use(router).mount('#app');
}
initApp();
