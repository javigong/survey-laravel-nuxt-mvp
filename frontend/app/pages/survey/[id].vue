<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div
      class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700"
    >
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
              {{ survey?.title || "Loading..." }}
            </h1>
            <p
              v-if="survey?.description"
              class="mt-2 text-gray-600 dark:text-gray-400"
            >
              {{ survey.description }}
            </p>
          </div>
          <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ questions.length }} question{{
              questions.length !== 1 ? "s" : ""
            }}
          </div>
        </div>
      </div>
    </div>

    <!-- Survey Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div v-if="loading" class="text-center py-12">
        <div
          class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"
        ></div>
        <p class="mt-4 text-gray-600 dark:text-gray-400">Loading survey...</p>
      </div>

      <div v-else-if="error" class="text-center py-12">
        <div class="text-red-600 dark:text-red-400 mb-4">
          <svg
            class="w-16 h-16 mx-auto"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
            />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
          Survey Not Found
        </h3>
        <p class="text-gray-600 dark:text-gray-400">{{ error }}</p>
        <button
          @click="loadSurvey"
          class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
        >
          Try Again
        </button>
      </div>

      <div v-else-if="submitted" class="text-center py-12">
        <div class="text-green-600 dark:text-green-400 mb-4">
          <svg
            class="w-16 h-16 mx-auto"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
          Thank You!
        </h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
          Your response has been submitted successfully.
        </p>
        <button
          @click="resetSurvey"
          class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
        >
          Submit Another Response
        </button>
      </div>

      <form v-else @submit.prevent="submitResponse" class="space-y-8">
        <!-- Questions -->
        <div
          v-for="(question, index) in questions"
          :key="question.id"
          class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
          <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
              {{ question.title }}
              <span v-if="question.is_required" class="text-red-500 ml-1"
                >*</span
              >
            </h3>
            <p
              v-if="question.description"
              class="text-sm text-gray-600 dark:text-gray-400"
            >
              {{ question.description }}
            </p>
          </div>

          <!-- Question Input Based on Type -->
          <div class="space-y-4">
            <!-- Short Text -->
            <input
              v-if="question.type === 'text_short'"
              v-model="answers[question.id]"
              type="text"
              :required="question.is_required"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              :placeholder="
                question.is_required ? 'Required field' : 'Optional'
              "
            />

            <!-- Long Text -->
            <textarea
              v-else-if="question.type === 'text_long'"
              v-model="answers[question.id]"
              :required="question.is_required"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              :placeholder="
                question.is_required ? 'Required field' : 'Optional'
              "
            ></textarea>

            <!-- Multiple Choice Single -->
            <div
              v-else-if="question.type === 'multiple_choice_single'"
              class="space-y-2"
            >
              <label
                v-for="(option, optionIndex) in question.options"
                :key="optionIndex"
                class="flex items-center space-x-3 p-3 rounded-md border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
              >
                <input
                  v-model="answers[question.id]"
                  type="radio"
                  :name="`question_${question.id}`"
                  :value="option"
                  :required="question.is_required"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                />
                <span class="text-gray-900 dark:text-white">{{ option }}</span>
              </label>
            </div>

            <!-- Multiple Choice Multiple -->
            <div
              v-else-if="question.type === 'multiple_choice_multiple'"
              class="space-y-2"
            >
              <label
                v-for="(option, optionIndex) in question.options"
                :key="optionIndex"
                class="flex items-center space-x-3 p-3 rounded-md border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
              >
                <input
                  v-model="answers[question.id]"
                  type="checkbox"
                  :value="option"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <span class="text-gray-900 dark:text-white">{{ option }}</span>
              </label>
            </div>

            <!-- Rating Scale -->
            <div v-else-if="question.type === 'rating_scale'" class="space-y-4">
              <div
                class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400"
              >
                <span>1</span>
                <span>5</span>
              </div>
              <div class="flex items-center space-x-2">
                <input
                  v-for="rating in 5"
                  :key="rating"
                  v-model="answers[question.id]"
                  type="radio"
                  :name="`question_${question.id}`"
                  :value="rating"
                  :required="question.is_required"
                  class="h-6 w-6 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                />
              </div>
            </div>

            <!-- Yes/No -->
            <div v-else-if="question.type === 'yes_no'" class="space-y-2">
              <label
                class="flex items-center space-x-3 p-3 rounded-md border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
              >
                <input
                  v-model="answers[question.id]"
                  type="radio"
                  :name="`question_${question.id}`"
                  value="yes"
                  :required="question.is_required"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                />
                <span class="text-gray-900 dark:text-white">Yes</span>
              </label>
              <label
                class="flex items-center space-x-3 p-3 rounded-md border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
              >
                <input
                  v-model="answers[question.id]"
                  type="radio"
                  :name="`question_${question.id}`"
                  value="no"
                  :required="question.is_required"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                />
                <span class="text-gray-900 dark:text-white">No</span>
              </label>
            </div>

            <!-- Dropdown -->
            <select
              v-else-if="question.type === 'dropdown'"
              v-model="answers[question.id]"
              :required="question.is_required"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
              <option value="">Select an option</option>
              <option
                v-for="(option, optionIndex) in question.options"
                :key="optionIndex"
                :value="option"
              >
                {{ option }}
              </option>
            </select>

            <!-- Date -->
            <input
              v-else-if="question.type === 'date'"
              v-model="answers[question.id]"
              type="date"
              :required="question.is_required"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />

            <!-- Time -->
            <input
              v-else-if="question.type === 'time'"
              v-model="answers[question.id]"
              type="time"
              :required="question.is_required"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />

            <!-- Date & Time -->
            <input
              v-else-if="question.type === 'datetime'"
              v-model="answers[question.id]"
              type="datetime-local"
              :required="question.is_required"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />

            <!-- File Upload -->
            <div v-else-if="question.type === 'file_upload'" class="space-y-2">
              <input
                @change="handleFileUpload(question.id, $event)"
                type="file"
                :required="question.is_required"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Maximum file size: 10MB
              </p>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div
          class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700"
        >
          <button
            type="submit"
            :disabled="submitting"
            class="px-6 py-3 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition"
          >
            <span v-if="submitting" class="flex items-center">
              <svg
                class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                ></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              Submitting...
            </span>
            <span v-else>Submit Response</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Survey, Question } from "~/stores/survey";
import { useRuntimeConfig } from "nuxt/app";
import { $fetch } from "ofetch";

definePageMeta({
  layout: false, // No layout for public survey
});

const config = useRuntimeConfig();
const route = useRoute();

const loading = ref(true);
const error = ref<string | null>(null);
const submitting = ref(false);
const submitted = ref(false);

const survey = ref<Survey | null>(null);
const questions = ref<Question[]>([]);
const answers = ref<Record<number, any>>({});

const loadSurvey = async () => {
  try {
    loading.value = true;
    error.value = null;

    const surveyId = route.params.id as string;

    // Fetch survey details
    const surveyResponse = await $fetch<{
      data: Survey;
      message: string;
    }>(`${config.public.apiBase}/api/v1/surveys/${surveyId}/public`, {
      method: "GET",
    });

    survey.value = surveyResponse.data;

    // Fetch questions
    const questionsResponse = await $fetch<{
      data: Question[];
    }>(`${config.public.apiBase}/api/v1/surveys/${surveyId}/questions/public`, {
      method: "GET",
    });

    questions.value = questionsResponse.data.sort((a, b) => a.order - b.order);

    // Initialize answers
    questions.value.forEach((question) => {
      if (question.type === "multiple_choice_multiple") {
        answers.value[question.id] = [];
      } else {
        answers.value[question.id] = "";
      }
    });
  } catch (err: any) {
    error.value = err.data?.message || "Failed to load survey";
  } finally {
    loading.value = false;
  }
};

const handleFileUpload = (questionId: number, event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];

  if (file) {
    // For now, just store the file name
    // In a real implementation, you'd upload the file to a server
    answers.value[questionId] = file.name;
  }
};

const submitResponse = async () => {
  try {
    submitting.value = true;

    const surveyId = parseInt(route.params.id as string);
    const respondentId = `respondent_${Date.now()}_${Math.random()
      .toString(36)
      .substr(2, 9)}`;

    // Prepare answers for submission
    const responseData = {
      survey_id: surveyId,
      respondent_id: respondentId,
      answers: Object.entries(answers.value)
        .map(([questionId, value]) => ({
          question_id: parseInt(questionId),
          value: value,
        }))
        .filter(
          (answer) =>
            answer.value !== "" &&
            answer.value !== null &&
            answer.value !== undefined
        ),
    };

    // Submit response
    await $fetch(
      `${config.public.apiBase}/api/v1/surveys/${surveyId}/responses`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: responseData,
      }
    );

    submitted.value = true;
  } catch (err: any) {
    error.value = err.data?.message || "Failed to submit response";
  } finally {
    submitting.value = false;
  }
};

const resetSurvey = () => {
  submitted.value = false;
  answers.value = {};
  questions.value.forEach((question) => {
    if (question.type === "multiple_choice_multiple") {
      answers.value[question.id] = [];
    } else {
      answers.value[question.id] = "";
    }
  });
};

// Load survey on mount
onMounted(() => {
  loadSurvey();
});
</script>
