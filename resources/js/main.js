import App from '@/App.vue';
import { registerPlugins } from '@core/utils/plugins';
import { createApp } from 'vue';
// main.js
import echo from '@/plugins/echo';

import Loader from '@/pages/loader/loader.vue';
import '@core-scss/template/index.scss';
import '@layouts/styles/index.scss';

// Create Vue app
const app = createApp(App);
app.component('Loader', Loader);
app.config.globalProperties.$echo = echo;
registerPlugins(app);
app.mount('#app');
