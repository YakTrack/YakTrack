import { createInertiaApp } from '@inertiajs/vue2'
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

createInertiaApp({
  resolve: name => {
    const pages = require.context('./Pages', true, /\.vue$/i)
    return pages(`./` + name + '.vue').default
  },
  setup({ el, App, props, plugin }) {
    Vue.use(plugin)

    new Vue({
      render: h => h(App, props),
    }).$mount(el)
  },
})