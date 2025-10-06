<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
          Sign in to your account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
          Or
          <NuxtLink to="/register" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
            create a new account
          </NuxtLink>
        </p>
      </div>

      <UiCard class="w-full">
        <UiCardHeader>
          <UiCardTitle>Login</UiCardTitle>
        </UiCardHeader>
        <UiCardContent>
          <form @submit.prevent="handleLogin" class="space-y-6">
            <div>
              <UiLabel for="email">Email address</UiLabel>
              <UiInput
                id="email"
                v-model="form.email"
                type="email"
                autocomplete="email"
                required
                class="mt-1"
                :disabled="loading"
              />
            </div>

            <div>
              <UiLabel for="password">Password</UiLabel>
              <UiInput
                id="password"
                v-model="form.password"
                type="password"
                autocomplete="current-password"
                required
                class="mt-1"
                :disabled="loading"
              />
            </div>

            <div v-if="error" class="text-red-600 text-sm">
              {{ error }}
            </div>

            <UiButton
              type="submit"
              class="w-full"
              :disabled="loading"
            >
              {{ loading ? 'Signing in...' : 'Sign in' }}
            </UiButton>
          </form>
        </UiCardContent>
      </UiCard>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { LoginCredentials } from '~/composables/useAuth'

const { login } = useAuth()
const authStore = useAuthStore()
const router = useRouter()

const form = reactive<LoginCredentials>({
  email: '',
  password: '',
})

const loading = ref(false)
const error = ref<string | null>(null)

// Redirect if already authenticated
if (authStore.isLoggedIn) {
  await router.push('/dashboard')
}

const handleLogin = async () => {
  loading.value = true
  error.value = null

  try {
    await login(form)
    await router.push('/dashboard')
  } catch (err: any) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}
</script>
