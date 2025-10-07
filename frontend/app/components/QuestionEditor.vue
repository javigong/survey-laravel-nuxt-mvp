<template>
  <div class="space-y-6">
    <!-- Question Type Selector -->
    <div>
      <label
        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
      >
        Question Type
      </label>
      <select
        v-model="localQuestion.type"
        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
      >
        <option
          v-for="type in questionTypes"
          :key="type.type"
          :value="type.type"
        >
          {{ type.icon }} {{ type.name }}
        </option>
      </select>
    </div>

    <!-- Question Title -->
    <div>
      <label
        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
      >
        Question Title *
      </label>
      <input
        v-model="localQuestion.title"
        type="text"
        required
        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
        placeholder="Enter your question..."
      />
    </div>

    <!-- Question Description -->
    <div>
      <label
        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
      >
        Description (Optional)
      </label>
      <textarea
        v-model="localQuestion.description"
        rows="2"
        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
        placeholder="Additional context or instructions..."
      />
    </div>

    <!-- Options for Multiple Choice Questions -->
    <div v-if="requiresOptions" class="space-y-4">
      <div>
        <label
          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
        >
          Options
        </label>
        <div class="space-y-2">
          <div
            v-for="(option, index) in localQuestion.options || []"
            :key="index"
            class="flex items-center space-x-2"
          >
            <input
              v-model="localQuestion.options[index]"
              type="text"
              class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              :placeholder="`Option ${index + 1}`"
            />
            <button
              @click="removeOption(index)"
              class="text-red-600 hover:text-red-900 dark:text-red-400 p-1"
              :disabled="(localQuestion.options?.length || 0) <= 2"
            >
              <svg
                class="w-4 h-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>
          <button
            @click="addOption"
            class="w-full px-3 py-2 text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 border border-dashed border-indigo-300 dark:border-indigo-600 rounded-md hover:bg-indigo-50 dark:hover:bg-indigo-900/20"
          >
            + Add Option
          </button>
        </div>
      </div>
    </div>

    <!-- Rating Scale Configuration -->
    <div v-if="localQuestion.type === 'rating_scale'" class="space-y-4">
      <div>
        <label
          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
        >
          Scale Range
        </label>
        <div class="flex items-center space-x-4">
          <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1"
              >Min</label
            >
            <input
              v-model.number="ratingConfig.min"
              type="number"
              min="1"
              max="10"
              class="w-20 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />
          </div>
          <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1"
              >Max</label
            >
            <input
              v-model.number="ratingConfig.max"
              type="number"
              min="2"
              max="10"
              class="w-20 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />
          </div>
          <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1"
              >Labels</label
            >
            <div class="flex space-x-2">
              <input
                v-model="ratingConfig.minLabel"
                type="text"
                placeholder="Poor"
                class="w-20 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
              />
              <input
                v-model="ratingConfig.maxLabel"
                type="text"
                placeholder="Excellent"
                class="w-20 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Validation Rules -->
    <div class="space-y-4">
      <div>
        <label
          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
        >
          Validation
        </label>
        <div class="space-y-2">
          <label class="flex items-center">
            <input
              v-model="localQuestion.is_required"
              type="checkbox"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            />
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
              Required question
            </span>
          </label>
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div
      class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700"
    >
      <button
        @click="$emit('cancel')"
        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600"
      >
        Cancel
      </button>
      <button
        @click="save"
        :disabled="!localQuestion.title?.trim()"
        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        {{ question.id ? "Update Question" : "Add Question" }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Question } from "~/stores/survey";

const props = defineProps<{
  question: Question | Partial<Question>;
  questionType?: string;
}>();

const emit = defineEmits<{
  save: [question: Partial<Question>];
  cancel: [];
}>();

const localQuestion = ref<Partial<Question>>({
  ...props.question,
  options: props.question.options ? [...props.question.options] : [],
});

const ratingConfig = ref({
  min: 1,
  max: 5,
  minLabel: "Poor",
  maxLabel: "Excellent",
});

const questionTypes = [
  { type: "text_short", name: "Short Text", icon: "📝" },
  { type: "text_long", name: "Long Text", icon: "📄" },
  { type: "multiple_choice_single", name: "Multiple Choice", icon: "🔘" },
  { type: "multiple_choice_multiple", name: "Checkboxes", icon: "☑️" },
  { type: "rating_scale", name: "Rating Scale", icon: "⭐" },
  { type: "yes_no", name: "Yes/No", icon: "✅" },
  { type: "dropdown", name: "Dropdown", icon: "📋" },
  { type: "date", name: "Date", icon: "📅" },
  { type: "time", name: "Time", icon: "🕐" },
  { type: "datetime", name: "Date & Time", icon: "📅" },
  { type: "file_upload", name: "File Upload", icon: "📎" },
];

const requiresOptions = computed(() => {
  return [
    "multiple_choice_single",
    "multiple_choice_multiple",
    "dropdown",
  ].includes(localQuestion.value.type || "");
});

const addOption = () => {
  if (!localQuestion.value.options) {
    localQuestion.value.options = [];
  }
  localQuestion.value.options.push("");
};

const removeOption = (index: number) => {
  if (localQuestion.value.options && localQuestion.value.options.length > 2) {
    localQuestion.value.options.splice(index, 1);
  }
};

const save = () => {
  if (!localQuestion.value.title?.trim()) return;

  const questionData = {
    ...localQuestion.value,
    validation_rules: {
      ...localQuestion.value.validation_rules,
      ...(localQuestion.value.type === "rating_scale"
        ? ratingConfig.value
        : {}),
    },
  };

  emit("save", questionData);
};

// Initialize options for multiple choice questions
watch(
  () => localQuestion.value.type,
  (newType) => {
    if (
      requiresOptions.value &&
      (!localQuestion.value.options || localQuestion.value.options.length === 0)
    ) {
      localQuestion.value.options = ["Option 1", "Option 2"];
    }
  },
  { immediate: true }
);
</script>
