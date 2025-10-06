// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },

  // Modules
  modules: [
    '@nuxtjs/tailwindcss',
    '@nuxtjs/color-mode',
    '@pinia/nuxt',
    '@vueuse/nuxt',
    'shadcn-nuxt'
  ],

  // Tailwind CSS configuration
  tailwindcss: {
    cssPath: '~/assets/css/tailwind.css'
  },

  // Color mode configuration
  colorMode: {
    preference: 'light',
    fallback: 'light',
    hid: 'nuxt-color-mode-script',
    globalName: '__NUXT_COLOR_MODE__',
    componentName: 'ColorScheme',
    classPrefix: '',
    classSuffix: '',
    storageKey: 'nuxt-color-mode'
  },

  // Shadcn configuration
  shadcn: {
    prefix: '',
    componentDir: '~/components/ui'
  },

  // Pinia configuration
  pinia: {
    storesDirs: ['./stores/**']
  },

  // TypeScript configuration
  typescript: {
    typeCheck: true
  },

  // Runtime config (environment variables)
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://127.0.0.1:8000'
    }
  },

  // CSS
  css: ['~/assets/css/main.css'],

  // Build configuration
  nitro: {
    preset: 'node-server'
  },

  // SSR configuration
  ssr: true,

  // App configuration
  app: {
    head: {
      title: 'Survey MVP',
      meta: [
        { name: 'description', content: 'Modern survey application built with Nuxt.js and Laravel' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' }
      ],
      link: [
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
      ]
    }
  }
})