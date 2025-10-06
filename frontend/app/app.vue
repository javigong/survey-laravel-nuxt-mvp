<template>
  <div>
    <NuxtRouteAnnouncer />
    <NuxtPage />
  </div>
</template>

<script setup lang="ts">
// Initialize auth store on app start
const authStore = useAuthStore()

// Initialize authentication state from localStorage
onMounted(() => {
  authStore.initializeAuth()
})
</script>
