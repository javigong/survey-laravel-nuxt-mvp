<template>
  <div>
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <!-- Back Button -->
        <NuxtLink
          to="/dashboard"
          class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 mb-6"
        >
          ← Back to Dashboard
        </NuxtLink>

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            My Surveys
          </h1>
          <NuxtLink
            to="/dashboard/surveys/create"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
          >
            + Create Survey
          </NuxtLink>
        </div>

        <!-- Filters -->
        <div class="mb-6 flex gap-4">
          <input
            v-model="filters.title"
            type="text"
            placeholder="Search by title..."
            class="flex-1 px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            @input="debouncedSearch"
          />
          <select
            v-model="filters.status"
            class="px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            @change="loadSurveys"
          >
            <option value="">All Statuses</option>
            <option value="draft">Draft</option>
            <option value="published">Published</option>
            <option value="closed">Closed</option>
          </select>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <p class="text-gray-600 dark:text-gray-400">Loading surveys...</p>
        </div>

        <!-- Error State -->
        <div
          v-else-if="error"
          class="bg-red-50 dark:bg-red-900/20 p-4 rounded-md"
        >
          <p class="text-red-600 dark:text-red-400">{{ error }}</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="surveys.length === 0" class="text-center py-12">
          <p class="text-gray-600 dark:text-gray-400 mb-4">
            No surveys found. Create your first survey!
          </p>
          <NuxtLink
            to="/dashboard/surveys/create"
            class="inline-block px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
          >
            + Create Survey
          </NuxtLink>
        </div>

        <!-- Surveys Table -->
        <div
          v-else
          class="bg-white dark:bg-gray-800 shadow overflow-hidden rounded-lg"
        >
          <table
            class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
          >
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Title
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Questions
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Responses
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Status
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Created
                </th>
                <th
                  class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Actions
                </th>
              </tr>
            </thead>
            <tbody
              class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
            >
              <tr v-for="survey in surveys" :key="survey.id">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div>
                    <div
                      class="text-sm font-medium text-gray-900 dark:text-white"
                    >
                      {{ survey.title }}
                    </div>
                    <div
                      v-if="survey.description"
                      class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-md"
                    >
                      {{ survey.description }}
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center space-x-2">
                    <span
                      class="text-sm font-medium text-gray-900 dark:text-white"
                    >
                      {{ survey.question_count || 0 }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                      questions
                    </span>
                  </div>
                  <div
                    v-if="survey.questions && survey.questions.length > 0"
                    class="mt-1"
                  >
                    <div class="flex flex-wrap gap-1">
                      <span
                        v-for="question in survey.questions.slice(0, 3)"
                        :key="question.id"
                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400"
                      >
                        {{ getQuestionTypeIcon(question.type) }}
                        {{ question.type_display }}
                      </span>
                      <span
                        v-if="survey.questions.length > 3"
                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400"
                      >
                        +{{ survey.questions.length - 3 }} more
                      </span>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center space-x-2">
                    <span
                      class="text-sm font-medium text-gray-900 dark:text-white"
                    >
                      {{ survey.response_count || 0 }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                      responses
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="getStatusClass(survey.status)"
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  >
                    {{ survey.status }}
                  </span>
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                >
                  {{ formatDate(survey.created_at) }}
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2"
                >
                  <NuxtLink
                    :to="`/dashboard/surveys/${survey.id}/edit`"
                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400"
                  >
                    Edit
                  </NuxtLink>
                  <button
                    @click="confirmDelete(survey)"
                    class="text-red-600 hover:text-red-900 dark:text-red-400"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="surveyToDelete"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div
        class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4"
      >
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
          Delete Survey
        </h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
          Are you sure you want to delete "{{ surveyToDelete.title }}"? This
          action cannot be undone.
        </p>
        <div class="flex justify-end space-x-4">
          <button
            @click="surveyToDelete = null"
            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600"
            :disabled="deleting"
          >
            Cancel
          </button>
          <button
            @click="handleDelete"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 disabled:opacity-50"
            :disabled="deleting"
          >
            {{ deleting ? "Deleting..." : "Delete" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useSurvey } from "~/composables/useSurvey";
import { useAuthStore } from "~/stores/auth";
import { useAuth } from "~/composables/useAuth";
import type { Survey } from "~/stores/survey";

const { fetchSurveys, deleteSurvey } = useSurvey();
const { logout } = useAuth();
const authStore = useAuthStore();
const router = useRouter();

const surveys = ref<Survey[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);
const deleting = ref(false);
const surveyToDelete = ref<Survey | null>(null);

const filters = reactive({
  title: "",
  status: "",
});

// Redirect if not authenticated
if (!authStore.isLoggedIn) {
  await router.push("/login");
}

const loadSurveys = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await fetchSurveys({
      title: filters.title || undefined,
      status: filters.status || undefined,
    });
    surveys.value = response.data;
  } catch (err: any) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
};

// Debounced search
let searchTimeout: NodeJS.Timeout;
const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    loadSurveys();
  }, 300);
};

const confirmDelete = (survey: Survey) => {
  surveyToDelete.value = survey;
};

const handleDelete = async () => {
  if (!surveyToDelete.value) return;

  deleting.value = true;
  try {
    await deleteSurvey(surveyToDelete.value.id);
    surveys.value = surveys.value.filter(
      (s) => s.id !== surveyToDelete.value!.id
    );
    surveyToDelete.value = null;
  } catch (err: any) {
    error.value = err.message;
  } finally {
    deleting.value = false;
  }
};

// Logout is handled by parent dashboard layout

const getStatusClass = (status: string) => {
  switch (status) {
    case "published":
      return "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400";
    case "closed":
      return "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300";
    default:
      return "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400";
  }
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

const getQuestionTypeIcon = (type: string) => {
  const icons: Record<string, string> = {
    multiple_choice_single: "🔘",
    multiple_choice_multiple: "☑️",
    text_short: "📝",
    text_long: "📄",
    rating_scale: "⭐",
    yes_no: "✅",
    dropdown: "📋",
    checkbox: "☑️",
    date: "📅",
    time: "🕐",
    datetime: "📅",
    file_upload: "📎",
  };
  return icons[type] || "❓";
};

// Load surveys on mount
onMounted(() => {
  loadSurveys();
});
</script>
