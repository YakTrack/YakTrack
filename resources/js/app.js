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
import taskSelect from './components/TaskSelect.vue';

const app = new Vue({
    el: '#app',
    components: {
        clientSelect: clientSelect,
        taskSelect: taskSelect,
        createTaskForm: createTaskForm,
    },
});
