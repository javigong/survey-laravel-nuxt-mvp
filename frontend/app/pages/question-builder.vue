<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Navigation -->
    <nav class="bg-white dark:bg-gray-800 shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">
              Survey MVP - Question Builder
            </h1>
          </div>
          <div class="flex items-center space-x-4">
            <NuxtLink
              to="/dashboard"
              class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600"
            >
              Back to Dashboard
            </NuxtLink>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Create New Survey
          </h1>
          <div class="flex space-x-3">
            <button
              @click="saveDraft"
              :disabled="loading"
              class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 disabled:opacity-50"
            >
              {{ loading ? "Saving..." : "Save Draft" }}
            </button>
            <button
              @click="publishSurvey"
              :disabled="loading || questions.length === 0"
              class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50"
            >
              {{ loading ? "Publishing..." : "Publish Survey" }}
            </button>
          </div>
        </div>

        <!-- Error Message -->
        <div
          v-if="error"
          class="bg-red-50 dark:bg-red-900/20 p-4 rounded-md mb-6"
        >
          <p class="text-red-600 dark:text-red-400 text-sm">{{ error }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Left Sidebar - Question Types -->
          <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
              <h3
                class="text-lg font-medium text-gray-900 dark:text-white mb-4"
              >
                Add Questions
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
                    placeholder="Brief description of your survey..."
                  />
                </div>
              </div>

              <!-- Question Types -->
              <div class="space-y-2">
                <h4
                  class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3"
                >
                  Question Types
                </h4>
                <button
                  v-for="questionType in questionTypes"
                  :key="questionType.type"
                  @click="addQuestion(questionType.type)"
                  class="w-full text-left p-3 rounded-md border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                >
                  <div class="flex items-center space-x-3">
                    <span class="text-lg">{{ questionType.icon }}</span>
                    <div>
                      <div
                        class="text-sm font-medium text-gray-900 dark:text-white"
                      >
                        {{ questionType.name }}
                      </div>
                      <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ questionType.description }}
                      </div>
                    </div>
                  </div>
                </button>
              </div>
            </div>
          </div>

          <!-- Main Content - Questions List -->
          <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
              <!-- Questions Header -->
              <div
                class="px-6 py-4 border-b border-gray-200 dark:border-gray-700"
              >
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                  Survey Questions ({{ questions.length }})
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                  Drag to reorder questions
                </p>
              </div>

              <!-- Questions List -->
              <div class="p-6">
                <div v-if="questions.length === 0" class="text-center py-12">
                  <div class="text-gray-400 text-6xl mb-4">📝</div>
                  <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                  >
                    No questions yet
                  </h3>
                  <p class="text-gray-500 dark:text-gray-400 mb-4">
                    Add questions from the sidebar to build your survey
                  </p>
                </div>

                <div v-else class="space-y-4">
                  <div
                    v-for="(question, index) in questions"
                    :key="question.id || `temp-${index}`"
                    class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:shadow-md transition-shadow"
                  >
                    <!-- Question Header -->
                    <div class="flex items-center justify-between mb-3">
                      <div class="flex items-center space-x-3">
                        <div class="flex items-center space-x-1 text-gray-400">
                          <span class="text-sm font-medium">{{
                            index + 1
                          }}</span>
                          <span class="text-lg">{{
                            getQuestionTypeIcon(question.type)
                          }}</span>
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
                      <div class="flex items-center space-x-2">
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

                    <!-- Question Preview -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-3">
                      <QuestionPreview :question="question" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Question Editor Modal -->
    <div
      v-if="editingQuestion !== null"
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
          :question-type="questions[editingQuestion]?.type || 'text_short'"
          @save="saveQuestion"
          @cancel="cancelEdit"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useSurvey } from "~/composables/useSurvey";
import { useAuthStore } from "~/stores/auth";
import type { Question } from "~/stores/survey";

const { createSurvey, createQuestion } = useSurvey();
const authStore = useAuthStore();
const router = useRouter();

const loading = ref(false);
const error = ref<string | null>(null);
const editingQuestion = ref<number | null>(null);

const surveyForm = reactive({
  title: "",
  description: "",
  status: "draft" as const,
});

const questions = ref<Question[]>([]);

const questionTypes = [
  {
    type: "text_short",
    name: "Short Text",
    description: "Single line text input",
    icon: "📝",
  },
  {
    type: "text_long",
    name: "Long Text",
    description: "Multi-line text area",
    icon: "📄",
  },
  {
    type: "multiple_choice_single",
    name: "Multiple Choice",
    description: "Select one option",
    icon: "🔘",
  },
  {
    type: "multiple_choice_multiple",
    name: "Checkboxes",
    description: "Select multiple options",
    icon: "☑️",
  },
  {
    type: "rating_scale",
    name: "Rating Scale",
    description: "Rate from 1-5 or 1-10",
    icon: "⭐",
  },
  {
    type: "yes_no",
    name: "Yes/No",
    description: "Simple yes or no question",
    icon: "✅",
  },
  {
    type: "dropdown",
    name: "Dropdown",
    description: "Select from dropdown menu",
    icon: "📋",
  },
  {
    type: "date",
    name: "Date",
    description: "Date picker",
    icon: "📅",
  },
  {
    type: "time",
    name: "Time",
    description: "Time picker",
    icon: "🕐",
  },
  {
    type: "datetime",
    name: "Date & Time",
    description: "Date and time picker",
    icon: "📅",
  },
  {
    type: "file_upload",
    name: "File Upload",
    description: "Upload files",
    icon: "📎",
  },
];

const newQuestion = (): Partial<Question> => ({
  title: "",
  description: "",
  type: "text_short",
  options: [],
  validation_rules: {},
  is_required: false,
  order: 0,
});

const addQuestion = (type: Question["type"]) => {
  const question = {
    ...newQuestion(),
    type,
    order: questions.value.length,
  } as Question;

  questions.value.push(question);
  editQuestion(questions.value.length - 1);
};

const editQuestion = (index: number) => {
  editingQuestion.value = index;
};

const duplicateQuestion = (index: number) => {
  const original = questions.value[index];
  const duplicated = {
    ...original,
    title: `${original.title} (Copy)`,
    order: questions.value.length,
  };
  questions.value.push(duplicated);
};

const deleteQuestion = (index: number) => {
  if (confirm("Are you sure you want to delete this question?")) {
    questions.value.splice(index, 1);
    // Update order for remaining questions
    questions.value.forEach((q, i) => {
      q.order = i;
    });
  }
};

const saveQuestion = (questionData: Partial<Question>) => {
  if (editingQuestion.value !== null) {
    if (editingQuestion.value === -1) {
      // New question
      questions.value.push(questionData as Question);
    } else {
      // Edit existing question
      questions.value[editingQuestion.value] = {
        ...questions.value[editingQuestion.value],
        ...questionData,
      };
    }
  }
  editingQuestion.value = null;
};

const cancelEdit = () => {
  editingQuestion.value = null;
};

const getQuestionTypeIcon = (type: string) => {
  const typeConfig = questionTypes.find((t) => t.type === type);
  return typeConfig?.icon || "❓";
};

const getQuestionTypeName = (type: string) => {
  const typeConfig = questionTypes.find((t) => t.type === type);
  return typeConfig?.name || type;
};

const saveDraft = async () => {
  await saveSurvey("draft");
};

const publishSurvey = async () => {
  await saveSurvey("published");
};

const saveSurvey = async (status: "draft" | "published") => {
  if (!surveyForm.title.trim()) {
    error.value = "Survey title is required";
    return;
  }

  loading.value = true;
  error.value = null;

  try {
    // Create survey
    const survey = await createSurvey({
      title: surveyForm.title,
      description: surveyForm.description,
      status,
    });

    // Create questions
    for (let i = 0; i < questions.value.length; i++) {
      const question = questions.value[i];
      await createQuestion({
        survey_id: survey.id,
        title: question.title,
        description: question.description,
        type: question.type,
        options: question.options,
        validation_rules: question.validation_rules,
        is_required: question.is_required,
        order: i,
      });
    }

    await router.push("/dashboard/surveys");
  } catch (err: any) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
};
</script>
