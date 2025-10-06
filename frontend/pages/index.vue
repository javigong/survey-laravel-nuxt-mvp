<template>
  <div>
    <!-- This page will redirect based on auth status -->
  </div>
</template>

<script setup lang="ts">
const authStore = useAuthStore()
const router = useRouter()

// Redirect based on authentication status
if (authStore.isLoggedIn) {
  await router.push('/dashboard')
} else {
  await router.push('/login')
}
</script>
