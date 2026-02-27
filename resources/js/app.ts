import '../css/app.css'

import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
// import type { DefineComponent } from 'vue'
// import { createApp, h } from 'vue'
import { initializeTheme } from './composables/useAppearance'
import axios from 'axios'
import Vue3EasyDataTable from 'vue3-easy-data-table'
import 'vue3-easy-data-table/dist/style.css'
import Toast, { POSITION } from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import { configureEcho } from '@laravel/echo-vue';
import './echo';
import { createApp, h, DefineComponent } from 'vue';
configureEcho({
    broadcaster: 'reverb',
});

// --------------------
// Axios global setup
// --------------------
axios.defaults.withCredentials = true
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

export async function postWithCsrf(url: string, data: object) {
  await axios.get('/sanctum/csrf-cookie')
  return axios.post(url, data)
}

export async function putWithCsrf(url: string, data: object) {
  await axios.get('/sanctum/csrf-cookie')
  return axios.put(url, data)
}

// --------------------
// Inertia app setup
// --------------------
const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
  title: (title) => (title ? `${title} - ${appName}` : appName),
  resolve: (name) =>
    resolvePageComponent(
      `./pages/${name}.vue`,
      import.meta.glob<DefineComponent>('./pages/**/*.vue'),
    ),
  setup({ el, App, props, plugin }) {
    const vueApp = createApp({ render: () => h(App, props) })
    vueApp.use(plugin)

    // --------------------
    // Global Plugins
    // --------------------
    vueApp.use(Toast, {
      position: POSITION.TOP_RIGHT,
      timeout: 3000,
      closeOnClick: true,
      pauseOnHover: true,
    })

    // Register global components
    vueApp.component('EasyDataTable', Vue3EasyDataTable)

    vueApp.mount(el)
  },
  progress: {
    color: '#FFFFFF',
  },
})

// Initialize light/dark mode on page load
initializeTheme()
