// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: "2025-07-15",
  devtools: { enabled: true },

  // Modules
  modules: [
    "@nuxtjs/tailwindcss",
    "@nuxtjs/color-mode",
    "@pinia/nuxt",
    "@vueuse/nuxt",
  ],

  // Nuxt 4 app directory structure
  // Pages are automatically discovered from app/pages/
  // Components are automatically discovered from app/components/
  // Composables are automatically discovered from app/composables/
  // No need for explicit configuration for these in Nuxt 4

  // Tailwind CSS configuration
  tailwindcss: {
    cssPath: "~/assets/css/tailwind.css",
  },

  // Color mode configuration
  colorMode: {
    preference: "light",
    fallback: "light",
    hid: "nuxt-color-mode-script",
    globalName: "__NUXT_COLOR_MODE__",
    componentName: "ColorScheme",
    classPrefix: "",
    classSuffix: "",
    storageKey: "nuxt-color-mode",
  },

  // Pinia stores are automatically discovered from app/stores/ in Nuxt 4

  // TypeScript configuration - temporarily disabled for debugging
  typescript: {
    typeCheck: false,
  },

  // Runtime config (environment variables)
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || "http://127.0.0.1:8000",
    },
  },

  // CSS will be imported in app.vue

  // Build configuration
  nitro: {
    preset: "node-server",
  },

  // SSR configuration
  ssr: true,

  // App configuration
  app: {
    head: {
      title: "Survey MVP",
      meta: [
        {
          name: "description",
          content: "Modern survey application built with Nuxt.js and Laravel",
        },
        { name: "viewport", content: "width=device-width, initial-scale=1" },
      ],
      link: [{ rel: "icon", type: "image/x-icon", href: "/favicon.ico" }],
    },
  },
});
