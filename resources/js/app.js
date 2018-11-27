/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('bootstrap');
require('./bootstrap');
window.Vue = require('vue');

window.events = new Vue();
window.token = document.head.querySelector('meta[name="csrf-token"]').content;

Vue.component('csrf-input', require('./components/CsrfInput.vue'));

import alert from './components/Alert.vue';
import clientSelect from './components/ClientSelect.vue';
import createTaskForm from './components/CreateTaskForm.vue';
import dropdown from './components/Dropdown.vue';
import invoiceSelect from './components/InvoiceSelect.vue';
import taskSelect from './components/TaskSelect.vue';
import indexSessionTable from './components/IndexSessionTable';
import dateTime from './filters/DateTime.js';

Vue.filter('secondsSince', dateTime.secondsSince);
Vue.filter('durationForHumans', dateTime.durationForHumans);

const app = new Vue({
    el: '#app',
    data: {
        alerts: [],
    },
    components: {
        alert: alert,
        clientSelect: clientSelect,
        createTaskForm: createTaskForm,
        dropdown: dropdown,
        indexSessionTable: indexSessionTable,
        invoiceSelect: invoiceSelect,
        taskSelect: taskSelect,
    },
    created() {
        window.events.$on('notify', (notification) => {
            this.alerts.push(notification);
        });
    }
});
