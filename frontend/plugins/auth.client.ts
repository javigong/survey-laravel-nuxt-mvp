export default defineNuxtPlugin(() => {
  const authStore = useAuthStore()

  // Initialize auth on client-side only
  if (process.client) {
    authStore.initializeAuth()
  }
})

