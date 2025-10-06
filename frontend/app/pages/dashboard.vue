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
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
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
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { useAuth } from "../composables/useAuth";

const { logout } = useAuth();
const authStore = useAuthStore();
const router = useRouter();

// Redirect to login if not authenticated
if (!authStore.isLoggedIn) {
  await router.push("/login");
}

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
