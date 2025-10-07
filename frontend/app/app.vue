<template>
  <div>
    <NuxtRouteAnnouncer />
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </div>
</template>

<script setup lang="ts">
// Import CSS
import "../assets/css/main.css";
import "../assets/css/tailwind.css";

// Initialize auth store on app start
const authStore = useAuthStore();

// Initialize authentication state from localStorage
onMounted(() => {
  authStore.initializeAuth();
});
</script>
