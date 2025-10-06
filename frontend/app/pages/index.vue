<template>
  <div
    class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900"
  >
    <div class="max-w-md w-full text-center space-y-8">
      <div>
        <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white">
          Survey MVP
        </h1>
        <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
          Create and manage surveys with ease
        </p>
      </div>

      <div class="space-y-4">
        <NuxtLink
          to="/register"
          class="block w-full px-4 py-3 text-center font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Get Started
        </NuxtLink>

        <NuxtLink
          to="/login"
          class="block w-full px-4 py-3 text-center font-medium text-indigo-600 bg-white dark:bg-gray-800 dark:text-indigo-400 border border-indigo-600 dark:border-indigo-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Sign In
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from "../stores/auth";

const authStore = useAuthStore();
const router = useRouter();

// Redirect to dashboard if already logged in
if (authStore.isLoggedIn) {
  await router.push("/dashboard");
}
</script>
