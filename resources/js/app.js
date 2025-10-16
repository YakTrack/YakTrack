import '../css/app.css';

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createPinia } from 'pinia'
import mitt from 'mitt'
import closeable from './directives/Closeable';
import dateTime from './filters/DateTime.js';
import buttonLink from '@/Shared/ButtonLink.vue';

createInertiaApp({
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue'),
    ),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })

    // Install Inertia plugin
    app.use(plugin)

    // Install Pinia
    app.use(createPinia())

    // Global directives
    app.directive('closeable', closeable)

    // Global properties (replaces Vue 2 mixins)
    app.config.globalProperties.route = window.route

    // Global properties for filters (Vue 3 doesn't have filters)
    app.config.globalProperties.$filters = {
      dateForHumans: dateTime.dateForHumans,
      durationForHumans: dateTime.durationForHumans,
      fromNow: dateTime.fromNow,
      isToday: dateTime.isToday,
      secondsSince: dateTime.secondsSince,
      toDateTimeForHumans: dateTime.toDateTimeForHumans,
      toDateTimeString: dateTime.toDateTimeString,
      totalDuration: dateTime.totalDuration,
    }

    // Global components
    app.component('buttonLink', buttonLink)

    // Events Bus using mitt (Vue 3 alternative)
    const emitter = mitt()
    app.config.globalProperties.$events = emitter
    window.events = emitter

    app.mount(el)
  },
})