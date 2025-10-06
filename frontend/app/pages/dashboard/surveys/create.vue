<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Navigation -->
    <nav class="bg-white dark:bg-gray-800 shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center space-x-8">
            <NuxtLink
              to="/dashboard"
              class="text-xl font-bold text-gray-900 dark:text-white"
            >
              Survey MVP
            </NuxtLink>
            <NuxtLink
              to="/dashboard/surveys"
              class="text-gray-600 dark:text-gray-300 hover:text-indigo-600"
            >
              Surveys
            </NuxtLink>
          </div>
          <div class="flex items-center space-x-4">
            <span class="text-gray-700 dark:text-gray-300">
              {{ authStore.user?.name }}
            </span>
            <button
              @click="handleLogout"
              class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <!-- Back Button -->
        <NuxtLink
          to="/dashboard/surveys"
          class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 mb-6"
        >
          ← Back to Surveys
        </NuxtLink>

        <!-- Header -->
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
          Create New Survey
        </h1>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Title -->
            <div>
              <label
                for="title"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
              >
                Survey Title *
              </label>
              <input
                id="title"
                v-model="form.title"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                :disabled="loading"
                placeholder="e.g., Customer Satisfaction Survey"
              />
            </div>

            <!-- Description -->
            <div>
              <label
                for="description"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
              >
                Description
              </label>
              <textarea
                id="description"
                v-model="form.description"
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                :disabled="loading"
                placeholder="Provide a brief description of your survey..."
              />
            </div>

            <!-- Status -->
            <div>
              <label
                for="status"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
              >
                Status
              </label>
              <select
                id="status"
                v-model="form.status"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                :disabled="loading"
              >
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="closed">Closed</option>
              </select>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Save as draft to edit later, or publish immediately
              </p>
            </div>

            <!-- Error Message -->
            <div
              v-if="error"
              class="bg-red-50 dark:bg-red-900/20 p-4 rounded-md"
            >
              <p class="text-red-600 dark:text-red-400 text-sm">{{ error }}</p>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-4">
              <NuxtLink
                to="/dashboard/surveys"
                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600"
              >
                Cancel
              </NuxtLink>
              <button
                type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="loading"
              >
                {{ loading ? "Creating..." : "Create Survey" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { useSurvey, type CreateSurveyData } from "~/composables/useSurvey";
import { useAuthStore } from "~/stores/auth";
import { useAuth } from "~/composables/useAuth";

const { createSurvey } = useSurvey();
const { logout } = useAuth();
const authStore = useAuthStore();
const router = useRouter();

const loading = ref(false);
const error = ref<string | null>(null);

const form = reactive<CreateSurveyData>({
  title: "",
  description: "",
  status: "draft",
});

// Redirect if not authenticated
if (!authStore.isLoggedIn) {
  await router.push("/login");
}

const handleSubmit = async () => {
  loading.value = true;
  error.value = null;

  try {
    await createSurvey(form);
    await router.push("/dashboard/surveys");
  } catch (err: any) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
};

const handleLogout = async () => {
  try {
    await logout();
    await router.push("/login");
  } catch (error: any) {
    await router.push("/login");
  }
};
</script>
