/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import 'vue-datetime/dist/vue-datetime.css'

window.Vue = require('vue');

window.events = new Vue();
window.token = document.head.querySelector('meta[name="csrf-token"]').content;

Vue.component('csrf-input', require('./components/CsrfInput.vue'));

import alert from './components/Alert.vue';
import clientSelect from './components/ClientSelect.vue';
import createTaskForm from './components/CreateTaskForm.vue';
import dateTime from './filters/DateTime.js';
import datetimePicker from 'vue-datetime';
import dropdown from './components/Dropdown.vue';
import indexSessionTable from './components/IndexSessionTable';
import invoiceSelect from './components/InvoiceSelect.vue';
import sprintSelect from './components/SprintSelect.vue';
import taskSelect from './components/TaskSelect.vue';
import timer from './components/Timer.vue';
import Router from './router.js';

window.router = new Router;

Vue.filter('secondsSince', dateTime.secondsSince);
Vue.filter('durationForHumans', dateTime.durationForHumans);
Vue.filter('toDateTimeString', dateTime.toDateTimeString);
Vue.filter('toDateTimeForHumans', dateTime.toDateTimeForHumans);

import closeable from './directives/Closeable.js';

Vue.use(datetimePicker);

const app = new Vue({
    el: '#app',
    data: {
        alerts: [],
        showFilters: false,
    },
    components: {
        alert: alert,
        clientSelect: clientSelect,
        createTaskForm: createTaskForm,
        dropdown: dropdown,
        indexSessionTable: indexSessionTable,
        invoiceSelect: invoiceSelect,
        sprintSelect: sprintSelect,
        taskSelect: taskSelect,
        timer: timer,
    },
    created() {
        window.events.$on('notify', (notification) => {
            this.alerts.push(notification);
        });
    },
});
