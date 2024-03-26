import {createApp} from 'vue'
import { createPinia } from 'pinia'
import CKEditor from '@ckeditor/ckeditor5-vue';

import router from "@/router";
import i18n from "@/plugins/i18n";
import App from "@/App";
import Notifications from 'notiwind'

const app = createApp(App)

app.use(createPinia());
app.use(CKEditor);

app.use(router);
app.use(i18n);
app.use(Notifications);
app.mount('#app');




