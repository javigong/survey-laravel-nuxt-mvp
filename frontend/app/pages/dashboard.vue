<template>
  <div>
    <!-- Main Content -->
    <div>
      <div class="px-4 py-6 sm:px-0">
        <!-- Debug info -->
        <div class="bg-yellow-100 p-4 mb-4">
          <p>Debug: Route path = "{{ route.path }}"</p>
          <p>Debug: Is dashboard? {{ route.path === "/dashboard" }}</p>
        </div>

        <!-- Show dashboard content only for /dashboard route -->
        <div
          v-if="route.path === '/dashboard'"
          class="bg-white dark:bg-gray-800 rounded-lg shadow p-6"
        >
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Welcome to Your Dashboard
          </h2>
          <p class="text-gray-600 dark:text-gray-400 mb-6">
            You're successfully logged in! Start managing your surveys.
          </p>

          <!-- Quick Actions -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <NuxtLink
              to="/dashboard/surveys"
              class="block p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition"
            >
              <h3
                class="text-lg font-semibold text-indigo-900 dark:text-indigo-300 mb-2"
              >
                📋 My Surveys
              </h3>
              <p class="text-sm text-indigo-700 dark:text-indigo-400">
                View and manage all your surveys
              </p>
            </NuxtLink>

            <NuxtLink
              to="/dashboard/surveys/create"
              class="block p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition"
            >
              <h3
                class="text-lg font-semibold text-green-900 dark:text-green-300 mb-2"
              >
                ➕ Create Survey
              </h3>
              <p class="text-sm text-green-700 dark:text-green-400">
                Start building a new survey
              </p>
            </NuxtLink>
          </div>

          <!-- User Info -->
          <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <p class="text-sm text-gray-500 dark:text-gray-500">
              Email: {{ authStore.user?.email }}
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">
              User ID: {{ authStore.user?.id }}
            </p>
          </div>
        </div>

        <!-- Show child pages for other dashboard routes -->
        <NuxtPage v-else />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from "~/stores/auth";

// Set layout
definePageMeta({
  layout: "dashboard",
});

const authStore = useAuthStore();
const route = useRoute();

// Debug route path
console.log("Current route path:", route.path);
</script>
