import App from '@/App.vue';
import { registerPlugins } from '@core/utils/plugins';
import { createApp } from 'vue';

// Styles
import '@core-scss/template/index.scss';
import '@layouts/styles/index.scss';

// Create Vue app
const app = createApp(App);

// Register plugins
registerPlugins(app);

// Mount Vue app
app.mount('#app');
