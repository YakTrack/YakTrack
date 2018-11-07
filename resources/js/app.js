/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('bootstrap');
require('./bootstrap');
window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('csrf-input', require('./components/CsrfInput.vue'));

import createTaskForm from './components/CreateTaskForm.vue';
import clientSelect from './components/ClientSelect.vue';
import invoiceSelect from './components/InvoiceSelect.vue';
import taskSelect from './components/TaskSelect.vue';
import sessionsComponent from './components/SessionsComponent.vue';
import sessionTask from './components/SessionTask.vue';
import thirdPartyApp from './components/ThirdPartyApp.vue';

const app = new Vue({
    el: '#app',
    components: {
        clientSelect: clientSelect,
        createTaskForm: createTaskForm,
        invoiceSelect: invoiceSelect,
        taskSelect: taskSelect,
        sessionsComponent: sessionsComponent,
        sessionTask: sessionTask,
        thirdPartyApp: thirdPartyApp,
    },
});
