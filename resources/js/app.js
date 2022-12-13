import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import App from '@/layouts/App.vue'

// Uncomment this  if you are using Laravel Echo
// import { Inertia } from '@inertiajs/inertia'

// Inertia.on('before', (event) => {
//   event.detail.visit.headers['X-Socket-ID'] = window.Echo.socketId()
// })

createInertiaApp({
  resolve: async (name) => {
    const page = (await resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue'))).default

    page.layout = App

    return page
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  },
})
