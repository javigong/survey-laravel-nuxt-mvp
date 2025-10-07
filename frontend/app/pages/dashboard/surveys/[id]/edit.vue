<template>
  <div class="flex flex-col lg:flex-row gap-6">
    <!-- Left Sidebar: Survey Info & Question Types -->
    <div class="lg:col-span-1">
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
          Survey Details
        </h3>

        <!-- Survey Basic Info -->
        <div class="mb-6 space-y-4">
          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
            >
              Survey Title *
            </label>
            <input
              v-model="surveyForm.title"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              placeholder="e.g., Customer Satisfaction Survey"
            />
          </div>
          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
            >
              Description
            </label>
            <textarea
              v-model="surveyForm.description"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              placeholder="Brief description of your survey..."
            />
          </div>
          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
            >
              Status
            </label>
            <select
              v-model="surveyForm.status"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
              <option value="draft">Draft</option>
              <option value="published">Published</option>
              <option value="closed">Closed</option>
            </select>
          </div>
        </div>

        <!-- Question Types -->
        <div class="space-y-2">
          <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
            Add Questions
          </h4>
          <button
            v-for="questionType in questionTypes"
            :key="questionType.type"
            @click="addQuestion(questionType.type)"
            class="flex items-center w-full p-3 rounded-md text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition"
          >
            <span class="text-lg">{{ questionType.icon }}</span>
            <div class="ml-3">
              <div class="text-sm font-medium text-gray-900 dark:text-white">
                {{ questionType.name }}
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ questionType.description }}
              </p>
            </div>
          </button>
        </div>
      </div>
    </div>

    <!-- Right Content: Question List & Preview -->
    <div class="lg:col-span-2 flex-1">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
          Edit Survey
        </h1>
        <div class="flex space-x-3">
          <button
            @click="saveSurvey"
            :disabled="loading"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50"
          >
            {{ loading ? "Saving..." : "Save Changes" }}
          </button>
          <button
            v-if="surveyForm.status === 'published'"
            @click="shareSurvey"
            class="px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-md hover:bg-indigo-100 dark:bg-indigo-900/20 dark:text-indigo-400 dark:border-indigo-800 dark:hover:bg-indigo-900/30"
          >
            Share Survey
          </button>
          <NuxtLink
            to="/dashboard/surveys"
            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600"
          >
            Back to Surveys
          </NuxtLink>
        </div>
      </div>

      <!-- Error Message -->
      <div
        v-if="error"
        class="bg-red-50 dark:bg-red-900/20 p-4 rounded-md mb-6"
      >
        <p class="text-red-600 dark:text-red-400 text-sm">{{ error }}</p>
      </div>

      <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <!-- Questions Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            Survey Questions ({{ questions.length }})
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            Drag to reorder questions
          </p>
        </div>

        <!-- No Questions State -->
        <div v-if="questions.length === 0" class="text-center py-12">
          <div class="text-gray-400 text-6xl mb-4">📝</div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
            No questions yet
          </h3>
          <p class="text-gray-600 dark:text-gray-400">
            Add questions from the sidebar to build your survey
          </p>
        </div>

        <!-- Questions List with Drag and Drop -->
        <div v-else>
          <draggable
            v-model="questions"
            :animation="200"
            ghost-class="opacity-50"
            chosen-class="bg-indigo-50 dark:bg-indigo-900/20"
            drag-class="shadow-lg"
            @end="onQuestionReorder"
            class="divide-y divide-gray-200 dark:divide-gray-700"
            item-key="id"
          >
            <template #item="{ element: question, index }">
              <div
                :key="question.id || `new-${index}`"
                class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-move group"
              >
                <div class="flex justify-between items-start">
                  <div class="flex items-start space-x-4 flex-1">
                    <!-- Drag Handle -->
                    <div class="flex items-center space-x-2">
                      <div
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-move"
                      >
                        <svg
                          class="w-5 h-5"
                          fill="currentColor"
                          viewBox="0 0 20 20"
                        >
                          <path
                            d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                          />
                        </svg>
                      </div>
                      <div class="flex items-center space-x-1 text-gray-400">
                        <span class="text-sm font-medium">{{ index + 1 }}</span>
                        <span class="text-lg">{{
                          getQuestionTypeIcon(question.type)
                        }}</span>
                      </div>
                    </div>
                    <div>
                      <h4
                        class="text-sm font-medium text-gray-900 dark:text-white"
                      >
                        {{ question.title || "Untitled Question" }}
                      </h4>
                      <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ getQuestionTypeName(question.type) }}
                        <span
                          v-if="question.is_required"
                          class="text-red-500 ml-1"
                          >*</span
                        >
                      </p>
                    </div>
                  </div>
                  <div class="flex space-x-2">
                    <button
                      @click="editQuestion(index)"
                      class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 text-sm"
                    >
                      Edit
                    </button>
                    <button
                      @click="duplicateQuestion(index)"
                      class="text-gray-600 hover:text-gray-900 dark:text-gray-400 text-sm"
                    >
                      Duplicate
                    </button>
                    <button
                      @click="deleteQuestion(index)"
                      class="text-red-600 hover:text-red-900 dark:text-red-400 text-sm"
                    >
                      Delete
                    </button>
                  </div>
                </div>
                <div class="mt-4 pl-8">
                  <QuestionPreview :question="question" />
                </div>
              </div>
            </template>
          </draggable>
        </div>
      </div>
    </div>
  </div>

  <!-- Question Editor Modal -->
  <div
    v-if="showQuestionEditor"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div
      class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto"
    >
      <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
        {{ editingQuestion === -1 ? "Add Question" : "Edit Question" }}
      </h3>

      <QuestionEditor
        :question="questions[editingQuestion] || newQuestion"
        @save="handleSaveQuestion"
        @cancel="showQuestionEditor = false"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { useSurvey } from "~/composables/useSurvey";
import { useAuthStore } from "~/stores/auth";
import type { Question } from "~/stores/survey";
import QuestionPreview from "~/components/QuestionPreview.vue";
import QuestionEditor from "~/components/QuestionEditor.vue";
import draggable from "vuedraggable";

definePageMeta({
  layout: "dashboard",
});

const {
  getSurvey,
  updateSurvey,
  fetchQuestions,
  createQuestion,
  updateQuestion,
  deleteQuestion: deleteQuestionApi,
  reorderQuestions,
} = useSurvey();
const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();

const loading = ref(false);
const error = ref<string | null>(null);

const surveyForm = reactive({
  title: "",
  description: "",
  status: "draft" as "draft" | "published" | "closed",
});

const questions = ref<Question[]>([]);
const showQuestionEditor = ref(false);
const editingQuestion = ref(-1); // -1 for new question, index for existing

const newQuestion = ref<Question>({
  id: 0, // Temporary ID for new questions
  survey_id: 0, // Will be set after survey creation
  title: "",
  description: null,
  type: "text_short",
  type_display: "Short Text",
  options: null,
  validation_rules: null,
  is_required: false,
  order: 0,
  created_at: new Date().toISOString(),
  updated_at: new Date().toISOString(),
});

const questionTypes = [
  {
    type: "text_short",
    name: "Short Text",
    icon: "📝",
    description: "Single line text input",
  },
  {
    type: "text_long",
    name: "Long Text",
    icon: "📄",
    description: "Multi-line text area",
  },
  {
    type: "multiple_choice_single",
    name: "Multiple Choice",
    icon: "🔘",
    description: "Select one option",
  },
  {
    type: "multiple_choice_multiple",
    name: "Checkboxes",
    icon: "☑️",
    description: "Select multiple options",
  },
  {
    type: "rating_scale",
    name: "Rating Scale",
    icon: "⭐",
    description: "Rate from 1-5 or 1-10",
  },
  {
    type: "yes_no",
    name: "Yes/No",
    icon: "✅",
    description: "Simple yes or no question",
  },
  {
    type: "dropdown",
    name: "Dropdown",
    icon: "📋",
    description: "Select from dropdown menu",
  },
  {
    type: "date",
    name: "Date",
    icon: "📅",
    description: "Date picker",
  },
  {
    type: "time",
    name: "Time",
    icon: "🕐",
    description: "Time picker",
  },
  {
    type: "datetime",
    name: "Date & Time",
    icon: "📅",
    description: "Date and time picker",
  },
  {
    type: "file_upload",
    name: "File Upload",
    icon: "📎",
    description: "Upload files",
  },
];

// Load survey data
const loadSurvey = async () => {
  try {
    const surveyId = parseInt(route.params.id as string);
    const survey = await getSurvey(surveyId);

    surveyForm.title = survey.title;
    surveyForm.description = survey.description || "";
    surveyForm.status = survey.status;

    // Load questions
    const questionsData = await fetchQuestions(surveyId);
    questions.value = questionsData;
  } catch (err: any) {
    error.value = err.message;
  }
};

// Methods
const addQuestion = (type: Question["type"]) => {
  newQuestion.value = {
    id: 0,
    survey_id: parseInt(route.params.id as string),
    title: "",
    description: null,
    type: type,
    type_display: getQuestionTypeName(type),
    options: null,
    validation_rules: null,
    is_required: false,
    order: questions.value.length,
    created_at: new Date().toISOString(),
    updated_at: new Date().toISOString(),
  };
  editingQuestion.value = -1;
  showQuestionEditor.value = true;
};

const editQuestion = (index: number) => {
  editingQuestion.value = index;
  showQuestionEditor.value = true;
};

const duplicateQuestion = (index: number) => {
  const original = questions.value[index];
  const duplicated = { ...original, id: 0, order: questions.value.length }; // Reset ID for new question
  questions.value.splice(index + 1, 0, duplicated);
};

const deleteQuestion = async (index: number) => {
  const question = questions.value[index];
  if (question.id && question.id !== 0) {
    // Delete from API if it exists
    try {
      await deleteQuestionApi(question.id);
    } catch (err: any) {
      error.value = err.message;
      return;
    }
  }
  questions.value.splice(index, 1);
};

const handleSaveQuestion = async (questionData: Partial<Question>) => {
  if (editingQuestion.value === -1) {
    // Add new question
    const newQ: Question = {
      ...newQuestion.value,
      ...questionData,
      order: questions.value.length,
    };
    questions.value.push(newQ);
  } else {
    // Edit existing question
    const question = questions.value[editingQuestion.value];
    if (question.id && question.id !== 0) {
      // Update existing question via API
      try {
        const updatedQuestion = await updateQuestion(question.id, questionData);
        questions.value[editingQuestion.value] = updatedQuestion;
      } catch (err: any) {
        error.value = err.message;
        return;
      }
    } else {
      // Update local question
      questions.value[editingQuestion.value] = {
        ...questions.value[editingQuestion.value],
        ...questionData,
      };
    }
  }
  showQuestionEditor.value = false;
};

const saveSurvey = async () => {
  loading.value = true;
  error.value = null;
  try {
    const surveyId = parseInt(route.params.id as string);

    // Update survey details
    await updateSurvey(surveyId, {
      title: surveyForm.title,
      description: surveyForm.description,
      status: surveyForm.status,
    });

    // Save questions
    for (const q of questions.value) {
      const questionData = {
        survey_id: surveyId,
        title: q.title,
        description: q.description,
        type: q.type,
        options: q.options,
        validation_rules: q.validation_rules,
        is_required: q.is_required,
        order: q.order,
      };

      if (q.id && q.id !== 0) {
        // Update existing question
        await updateQuestion(q.id, questionData);
      } else {
        // Create new question
        await createQuestion(questionData);
      }
    }

    router.push("/dashboard/surveys");
  } catch (err: any) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
};

const getQuestionTypeIcon = (type: string) => {
  const typeConfig = questionTypes.find((t) => t.type === type);
  return typeConfig?.icon || "❓";
};

const getQuestionTypeName = (type: string) => {
  const typeConfig = questionTypes.find((t) => t.type === type);
  return typeConfig?.name || type;
};

const onQuestionReorder = async () => {
  try {
    const surveyId = parseInt(route.params.id as string);

    // Update the order property for each question based on new position
    questions.value.forEach((question, index) => {
      question.order = index;
    });

    // Get question IDs in the new order
    const questionIds = questions.value
      .filter((q) => q.id && q.id !== 0) // Only include saved questions
      .map((q) => q.id!);

    if (questionIds.length > 0) {
      await reorderQuestions(surveyId, questionIds);
    }
  } catch (err: any) {
    console.error("Failed to reorder questions:", err);
    error.value = "Failed to reorder questions";
  }
};

const shareSurvey = () => {
  const surveyId = route.params.id as string;
  const publicUrl = `${window.location.origin}/survey/${surveyId}`;

  // Copy to clipboard
  navigator.clipboard
    .writeText(publicUrl)
    .then(() => {
      alert("Survey link copied to clipboard!");
    })
    .catch(() => {
      // Fallback: show the URL in a prompt
      prompt("Copy this survey link:", publicUrl);
    });
};

// Load survey data on mount
onMounted(async () => {
  if (!authStore.isLoggedIn) {
    router.push("/login");
    return;
  }

  await loadSurvey();
});
</script>
