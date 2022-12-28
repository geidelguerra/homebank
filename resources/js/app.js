import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { router } from '@inertiajs/core'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import App from '@/layouts/App.vue'
import { date, money } from '@/utils'
import Echo from '@ably/laravel-echo';
import * as Ably from 'ably';

window.Ably = Ably

window.Echo = new Echo({
  broadcaster: 'ably'
});

window.Echo.connector.ably.connection.on(stateChange => {
  console.log('window.Echo.connector.ably.connection.on:', stateChange)

  if (stateChange.current === 'connected') {
    console.log('connected to ably server');
  }
});

router.on('before', (event) => {
  event.detail.visit.headers['X-Socket-ID'] = window.Echo.socketId()
})

createInertiaApp({
  resolve: async (name) => {
    const page = (await resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue'))).default

    page.layout = App

    return page
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use({
        install(app) {
          app.config.globalProperties.route = window.route
          app.config.globalProperties.$date = date
          app.config.globalProperties.$money = money
        }
      })
      .mount(el)
  },
})
