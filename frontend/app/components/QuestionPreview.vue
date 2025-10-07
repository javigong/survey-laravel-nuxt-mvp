<template>
  <div class="space-y-3">
    <!-- Question Title -->
    <div>
      <label class="block text-sm font-medium text-gray-900 dark:text-white">
        {{ question.title || "Untitled Question" }}
        <span v-if="question.is_required" class="text-red-500">*</span>
      </label>
      <p
        v-if="question.description"
        class="text-sm text-gray-500 dark:text-gray-400 mt-1"
      >
        {{ question.description }}
      </p>
    </div>

    <!-- Question Input Preview -->
    <div class="space-y-2">
      <!-- Text Inputs -->
      <input
        v-if="question.type === 'text_short'"
        type="text"
        :placeholder="
          question.is_required ? 'Required field' : 'Optional field'
        "
        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
        disabled
      />

      <!-- Text Area -->
      <textarea
        v-else-if="question.type === 'text_long'"
        :placeholder="
          question.is_required ? 'Required field' : 'Optional field'
        "
        rows="3"
        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
        disabled
      />

      <!-- Multiple Choice Single -->
      <div
        v-else-if="question.type === 'multiple_choice_single'"
        class="space-y-2"
      >
        <div
          v-for="(option, index) in question.options || [
            'Option 1',
            'Option 2',
          ]"
          :key="index"
          class="flex items-center"
        >
          <input
            type="radio"
            :name="`preview-${question.id || 'temp'}`"
            :id="`preview-${question.id || 'temp'}-${index}`"
            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
            disabled
          />
          <label
            :for="`preview-${question.id || 'temp'}-${index}`"
            class="ml-2 text-sm text-gray-700 dark:text-gray-300"
          >
            {{ option }}
          </label>
        </div>
      </div>

      <!-- Multiple Choice Multiple -->
      <div
        v-else-if="question.type === 'multiple_choice_multiple'"
        class="space-y-2"
      >
        <div
          v-for="(option, index) in question.options || [
            'Option 1',
            'Option 2',
          ]"
          :key="index"
          class="flex items-center"
        >
          <input
            type="checkbox"
            :id="`preview-multi-${question.id || 'temp'}-${index}`"
            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            disabled
          />
          <label
            :for="`preview-multi-${question.id || 'temp'}-${index}`"
            class="ml-2 text-sm text-gray-700 dark:text-gray-300"
          >
            {{ option }}
          </label>
        </div>
      </div>

      <!-- Rating Scale -->
      <div v-else-if="question.type === 'rating_scale'" class="space-y-2">
        <div class="flex items-center space-x-2">
          <span class="text-sm text-gray-500 dark:text-gray-400">1</span>
          <div class="flex space-x-1">
            <input
              v-for="i in 5"
              :key="i"
              type="radio"
              :name="`rating-${question.id || 'temp'}`"
              :id="`rating-${question.id || 'temp'}-${i}`"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
              disabled
            />
          </div>
          <span class="text-sm text-gray-500 dark:text-gray-400">5</span>
        </div>
      </div>

      <!-- Yes/No -->
      <div v-else-if="question.type === 'yes_no'" class="space-y-2">
        <div class="flex items-center space-x-4">
          <div class="flex items-center">
            <input
              type="radio"
              :name="`yesno-${question.id || 'temp'}`"
              :id="`yes-${question.id || 'temp'}`"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
              disabled
            />
            <label
              :for="`yes-${question.id || 'temp'}`"
              class="ml-2 text-sm text-gray-700 dark:text-gray-300"
            >
              Yes
            </label>
          </div>
          <div class="flex items-center">
            <input
              type="radio"
              :name="`yesno-${question.id || 'temp'}`"
              :id="`no-${question.id || 'temp'}`"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
              disabled
            />
            <label
              :for="`no-${question.id || 'temp'}`"
              class="ml-2 text-sm text-gray-700 dark:text-gray-300"
            >
              No
            </label>
          </div>
        </div>
      </div>

      <!-- Dropdown -->
      <select
        v-else-if="question.type === 'dropdown'"
        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
        disabled
      >
        <option value="">Select an option...</option>
        <option
          v-for="(option, index) in question.options || [
            'Option 1',
            'Option 2',
          ]"
          :key="index"
          :value="option"
        >
          {{ option }}
        </option>
      </select>

      <!-- Date -->
      <input
        v-else-if="question.type === 'date'"
        type="date"
        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
        disabled
      />

      <!-- Time -->
      <input
        v-else-if="question.type === 'time'"
        type="time"
        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
        disabled
      />

      <!-- DateTime -->
      <input
        v-else-if="question.type === 'datetime'"
        type="datetime-local"
        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
        disabled
      />

      <!-- File Upload -->
      <div v-else-if="question.type === 'file_upload'" class="space-y-2">
        <div
          class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center"
        >
          <div class="text-gray-400 text-2xl mb-2">📎</div>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            Click to upload or drag and drop
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Question } from "~/stores/survey";

defineProps<{
  question: Question | Partial<Question>;
}>();
</script>
