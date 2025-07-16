import './bootstrap';
import { createApp } from 'vue';

import ReductionList from './components/ReductionList.vue';

const app = createApp({});
app.component('reduction-list', ReductionList);
app.mount('#app');
