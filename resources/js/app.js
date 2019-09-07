import { InertiaApp } from '@inertiajs/inertia-vue'
import PortalVue from 'portal-vue'
import dateTime from './filters/DateTime.js';
import datetimePicker from 'vue-datetime';
import Vue from 'vue'

Vue.config.productionTip = false
Vue.mixin({ methods: { route: window.route } })
Vue.use(InertiaApp)
Vue.use(PortalVue)
Vue.use(datetimePicker);

Vue.filter('secondsSince', dateTime.secondsSince);
Vue.filter('durationForHumans', dateTime.durationForHumans);
Vue.filter('totalDurationOfSessions', dateTime.totalDurationOfSessions);
Vue.filter('toDateTimeString', dateTime.toDateTimeString);
Vue.filter('toDateTimeForHumans', dateTime.toDateTimeForHumans);

let app = document.getElementById('app')

window.events = new Vue();

new Vue({
    render: h => h(InertiaApp, {
        props: {
            initialPage: JSON.parse(app.dataset.page),
            resolveComponent: name => import(`@/Pages/${name}`).then(module => module.default),
        },
    }),
}).$mount(app)