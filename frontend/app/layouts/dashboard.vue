<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Navigation -->
    <nav class="bg-white dark:bg-gray-800 shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">
              Survey MVP
            </h1>
          </div>
          <div class="flex items-center space-x-4">
            <span class="text-gray-700 dark:text-gray-300">
              {{ authStore.user?.name }}
            </span>
            <button
              @click="handleLogout"
              class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="bg-red-100 p-4 mb-4">
          <p>Layout is working! This should show the page content below:</p>
        </div>
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { useAuth } from "~/composables/useAuth";
import { useAuthStore } from "~/stores/auth";

const { logout } = useAuth();
const authStore = useAuthStore();
const router = useRouter();

// Redirect to login if not authenticated (only on client side)
onMounted(() => {
  if (!authStore.isLoggedIn) {
    router.push("/login");
  }
});

const handleLogout = async () => {
  try {
    await logout();
    await router.push("/login");
  } catch (error: any) {
    console.error("Logout failed:", error);
    // Force logout locally even if API fails
    await router.push("/login");
  }
};
</script>
