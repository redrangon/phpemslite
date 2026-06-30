import "@/framework/utils/extension.js"
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import loadUI from '@/framework/ui'
import '@/assets/css/main.css'
import wasmCrypto from "@/framework/utils/wasmCrypto.js";

const app = createApp(App);
async function initApp() {
    await loadUI(app);
    app.use(createPinia()).use(router).mount('#app');
}
initApp();
const domain = window.location.hostname;
try{
    wasmCrypto.init(domain, '02254e2bdf2130c632a898d16abaa90c72dc904da0a9d795732d45e2b559c960');
}catch (e) {
    //console.log('授权验证失败',e.message, domain);
    //
}