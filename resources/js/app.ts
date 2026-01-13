// resources/js/app.ts
import '../css/app.css'

import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import type { DefineComponent } from 'vue'
import { createApp, h } from 'vue'
import { initializeTheme } from './composables/useAppearance'
import axios from 'axios'
import Vue3EasyDataTable from 'vue3-easy-data-table'
import 'vue3-easy-data-table/dist/style.css'

// --------------------
// Axios global setup
// --------------------
axios.defaults.withCredentials = true               // send cookies for Sanctum
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// Optional: helper for POST requests with CSRF
export async function postWithCsrf(url: string, data: object) {
  await axios.get('/sanctum/csrf-cookie')           // ensure CSRF token cookie
  return axios.post(url, data)
}

// Similarly, you can make helpers for PUT, PATCH, DELETE:
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
