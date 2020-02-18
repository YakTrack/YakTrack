import { InertiaApp } from '@inertiajs/inertia-vue'
import PortalVue from 'portal-vue'
import closeable from './directives/Closeable';
import dateTime from './filters/DateTime.js';
import Vue from 'vue'

Vue.directive('closeable', closeable);

Vue.config.productionTip = false

Vue.mixin({
    methods: {
        route: window.route,
    }
})

Vue.use(InertiaApp)
Vue.use(PortalVue)

Vue.filter('dateForHumans', dateTime.dateForHumans);
Vue.filter('durationForHumans', dateTime.durationForHumans);
Vue.filter('secondsSince', dateTime.secondsSince);
Vue.filter('fromNow', dateTime.fromNow);
Vue.filter('toDateTimeForHumans', dateTime.toDateTimeForHumans);
Vue.filter('toDateTimeString', dateTime.toDateTimeString);
Vue.filter('totalDuration', dateTime.totalDuration);

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