import App from '@/App.vue';
import { registerPlugins } from '@core/utils/plugins';
import { createApp } from 'vue';
import { echo } from './plugins/echo';
app.config.globalProperties.$echo = echo;

// Styles
import '@core-scss/template/index.scss';
import '@layouts/styles/index.scss';

// Create Vue app
const app = createApp(App);

// Register plugins
registerPlugins(app);

// Mount Vue app
app.mount('#app');
