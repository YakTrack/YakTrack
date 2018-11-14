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

import axios from 'axios';
import clientSelect from './components/ClientSelect.vue';
import createTaskForm from './components/CreateTaskForm.vue';
import dropdown from './components/Dropdown.vue';
import invoiceSelect from './components/InvoiceSelect.vue';
import taskSelect from './components/TaskSelect.vue';
import indexSessionTable from './components/IndexSessionTable';


const app = new Vue({
    el: '#app',
    components: {
        clientSelect: clientSelect,
        createTaskForm: createTaskForm,
        dropdown: dropdown,
        indexSessionTable: indexSessionTable,
        invoiceSelect: invoiceSelect,
        taskSelect: taskSelect,
    },
});
