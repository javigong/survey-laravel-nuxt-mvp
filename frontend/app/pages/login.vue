<template>
  <div
    class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8"
  >
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div>
        <h2
          class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white"
        >
          Sign in to your account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
          Or
          <NuxtLink
            to="/register"
            class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400"
          >
            create a new account
          </NuxtLink>
        </p>
      </div>

      <!-- Card -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form @submit.prevent="handleLogin" class="space-y-6">
          <!-- Email -->
          <div>
            <label
              for="email"
              class="block text-sm font-medium text-gray-700 dark:text-gray-300"
            >
              Email address
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              autocomplete="email"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              :disabled="loading"
            />
          </div>

          <!-- Password -->
          <div>
            <label
              for="password"
              class="block text-sm font-medium text-gray-700 dark:text-gray-300"
            >
              Password
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              autocomplete="current-password"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              :disabled="loading"
            />
          </div>

          <!-- Error Message -->
          <div v-if="error" class="text-red-600 text-sm">
            {{ error }}
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="loading"
          >
            {{ loading ? "Signing in..." : "Sign in" }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuth } from "../composables/useAuth";
import type { LoginCredentials } from "../composables/useAuth";

const { login } = useAuth();
const authStore = useAuthStore();
const router = useRouter();

const form = reactive<LoginCredentials>({
  email: "",
  password: "",
});

const loading = ref(false);
const error = ref<string | null>(null);

// Redirect if already authenticated
if (authStore.isLoggedIn) {
  await router.push("/dashboard");
}

const handleLogin = async () => {
  loading.value = true;
  error.value = null;

  try {
    await login(form);
    await router.push("/dashboard");
  } catch (err: any) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
};
</script>
