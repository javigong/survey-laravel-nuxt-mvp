<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
          Create your account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
          Or
          <NuxtLink to="/login" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
            sign in to existing account
          </NuxtLink>
        </p>
      </div>

      <UiCard class="w-full">
        <UiCardHeader>
          <UiCardTitle>Register</UiCardTitle>
        </UiCardHeader>
        <UiCardContent>
          <form @submit.prevent="handleRegister" class="space-y-6">
            <div>
              <UiLabel for="name">Full Name</UiLabel>
              <UiInput
                id="name"
                v-model="form.name"
                type="text"
                autocomplete="name"
                required
                class="mt-1"
                :disabled="loading"
              />
            </div>

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
                autocomplete="new-password"
                required
                class="mt-1"
                :disabled="loading"
              />
            </div>

            <div>
              <UiLabel for="password_confirmation">Confirm Password</UiLabel>
              <UiInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                autocomplete="new-password"
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
              {{ loading ? 'Creating account...' : 'Create account' }}
            </UiButton>
          </form>
        </UiCardContent>
      </UiCard>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { RegisterData } from '~/composables/useAuth'

const { register } = useAuth()
const authStore = useAuthStore()
const router = useRouter()

const form = reactive<RegisterData>({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const loading = ref(false)
const error = ref<string | null>(null)

// Redirect if already authenticated
if (authStore.isLoggedIn) {
  await router.push('/dashboard')
}

const handleRegister = async () => {
  loading.value = true
  error.value = null

  try {
    await register(form)
    await router.push('/dashboard')
  } catch (err: any) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}
</script>
