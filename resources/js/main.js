import App from '@/App.vue';
import { registerPlugins } from '@core/utils/plugins';
import { createApp } from 'vue';
// main.js
import echo from './plugins/echo'; // ✅ If using default export


// Styles
import '@core-scss/template/index.scss';
import '@layouts/styles/index.scss';

// Create Vue app
const app = createApp(App);
app.config.globalProperties.$echo = echo;
// Register plugins
registerPlugins(app);

// Mount Vue app
app.mount('#app');
