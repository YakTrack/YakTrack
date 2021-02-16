import { App, plugin } from '@inertiajs/inertia-vue'
import PortalVue from 'portal-vue'
import closeable from './directives/Closeable';
import dateTime from './filters/DateTime.js';
import Vue from 'vue'
import buttonLink from '@/Shared/ButtonLink';

// Global directives
Vue.directive('closeable', closeable);

// Global config
Vue.config.productionTip = false

// Global mixins
Vue.mixin({
    methods: {
        route: window.route,
    }
})

// Plugins
Vue.use(plugin)
Vue.use(PortalVue)

// Global filters
Vue.filter('dateForHumans', dateTime.dateForHumans);
Vue.filter('durationForHumans', dateTime.durationForHumans);
Vue.filter('fromNow', dateTime.fromNow);
Vue.filter('isToday', dateTime.isToday);
Vue.filter('secondsSince', dateTime.secondsSince);
Vue.filter('toDateTimeForHumans', dateTime.toDateTimeForHumans);
Vue.filter('toDateTimeString', dateTime.toDateTimeString);
Vue.filter('totalDuration', dateTime.totalDuration);

// Global components
Vue.component('buttonLink', buttonLink);

// Events Bus
window.events = new Vue();

// Root Vue instance
let app = document.getElementById('app')

new Vue({
    render: h => h(App, {
        props: {
            initialPage: JSON.parse(app.dataset.page),
            resolveComponent: name => import(`@/Pages/${name}`).then(module => module.default),
        },
    }),
}).$mount(app)