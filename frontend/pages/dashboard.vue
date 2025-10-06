<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Navigation -->
    <nav class="bg-white dark:bg-gray-800 shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
              Survey Dashboard
            </h1>
          </div>
          <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-700 dark:text-gray-300">
              Welcome, {{ authStore.user?.name }}
            </span>
            <UiButton
              variant="outline"
              size="sm"
              @click="handleLogout"
              :disabled="logoutLoading"
            >
              {{ logoutLoading ? 'Signing out...' : 'Sign out' }}
            </UiButton>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Your Surveys</h2>
            <p class="text-gray-600 dark:text-gray-400">Manage and create surveys</p>
          </div>
          <UiButton @click="showCreateDialog = true">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Survey
          </UiButton>
        </div>

        <!-- Surveys grid -->
        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
          <p class="mt-4 text-gray-600 dark:text-gray-400">Loading surveys...</p>
        </div>

        <div v-else-if="surveys.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No surveys</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first survey.</p>
          <div class="mt-6">
            <UiButton @click="showCreateDialog = true">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Create Survey
            </UiButton>
          </div>
        </div>

        <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          <UiCard
            v-for="survey in surveys"
            :key="survey.id"
            class="cursor-pointer hover:shadow-lg transition-shadow"
            @click="viewSurvey(survey)"
          >
            <UiCardHeader>
              <UiCardTitle class="flex items-center justify-between">
                <span class="truncate">{{ survey.title }}</span>
                <span
                  :class="[
                    'px-2 py-1 text-xs rounded-full',
                    survey.status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                    survey.status === 'draft' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                    'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                  ]"
                >
                  {{ survey.status }}
                </span>
              </UiCardTitle>
            </UiCardHeader>
            <UiCardContent>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                {{ survey.description || 'No description' }}
              </p>
              <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                <span>Created {{ formatDate(survey.created_at) }}</span>
                <div class="flex space-x-2">
                  <button
                    @click.stop="editSurvey(survey)"
                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400"
                  >
                    Edit
                  </button>
                  <button
                    @click.stop="deleteSurvey(survey)"
                    class="text-red-600 hover:text-red-900 dark:text-red-400"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </UiCardContent>
          </UiCard>
        </div>
      </div>
    </main>

    <!-- Create Survey Dialog -->
    <Teleport to="body">
      <div
        v-if="showCreateDialog"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        @click="showCreateDialog = false"
      >
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4" @click.stop>
          <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Create New Survey</h3>
          <form @submit.prevent="handleCreateSurvey" class="space-y-4">
            <div>
              <UiLabel for="title">Title</UiLabel>
              <UiInput
                id="title"
                v-model="createForm.title"
                required
                class="mt-1"
                :disabled="createLoading"
              />
            </div>
            <div>
              <UiLabel for="description">Description (optional)</UiLabel>
              <textarea
                v-model="createForm.description"
                class="mt-1 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                rows="3"
                :disabled="createLoading"
              />
            </div>
            <div class="flex justify-end space-x-3">
              <UiButton
                type="button"
                variant="outline"
                @click="showCreateDialog = false"
                :disabled="createLoading"
              >
                Cancel
              </UiButton>
              <UiButton
                type="submit"
                :disabled="createLoading"
              >
                {{ createLoading ? 'Creating...' : 'Create Survey' }}
              </UiButton>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import type { Survey, CreateSurveyData } from '~/composables/useSurvey'

const { logout } = useAuth()
const { fetchSurveys, createSurvey, deleteSurvey: deleteSurveyApi } = useSurvey()
const authStore = useAuthStore()
const surveyStore = useSurveyStore()
const router = useRouter()

// Redirect if not authenticated
if (!authStore.isLoggedIn) {
  await router.push('/login')
}

// Reactive data
const surveys = ref<Survey[]>([])
const loading = ref(true)
const logoutLoading = ref(false)
const showCreateDialog = ref(false)
const createLoading = ref(false)

const createForm = reactive<CreateSurveyData>({
  title: '',
  description: '',
  status: 'draft',
})

// Load surveys on mount
onMounted(async () => {
  await loadSurveys()
})

// Methods
const loadSurveys = async () => {
  loading.value = true
  try {
    const response = await fetchSurveys()
    surveys.value = response.data
    surveyStore.setSurveys(response.data)
  } catch (error) {
    console.error('Failed to load surveys:', error)
  } finally {
    loading.value = false
  }
}

const handleLogout = async () => {
  logoutLoading.value = true
  try {
    await logout()
    await router.push('/login')
  } catch (error) {
    console.error('Logout error:', error)
  } finally {
    logoutLoading.value = false
  }
}

const handleCreateSurvey = async () => {
  createLoading.value = true
  try {
    const survey = await createSurvey(createForm)
    surveyStore.addSurvey(survey)
    surveys.value.unshift(survey)
    showCreateDialog.value = false

    // Reset form
    createForm.title = ''
    createForm.description = ''

    // Show success message
    alert('Survey created successfully!')
  } catch (error: any) {
    alert(error.message || 'Failed to create survey')
  } finally {
    createLoading.value = false
  }
}

const viewSurvey = (survey: Survey) => {
  router.push(`/surveys/${survey.id}`)
}

const editSurvey = (survey: Survey) => {
  router.push(`/surveys/${survey.id}/edit`)
}

const handleDeleteSurvey = async (survey: Survey) => {
  if (!confirm(`Are you sure you want to delete "${survey.title}"?`)) {
    return
  }

  try {
    await deleteSurveyApi(survey.id)
    surveyStore.removeSurvey(survey.id)
    surveys.value = surveys.value.filter(s => s.id !== survey.id)
  } catch (error: any) {
    alert(error.message || 'Failed to delete survey')
  }
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString()
}

// Auth middleware
definePageMeta({
  middleware: 'auth'
})
</script>
