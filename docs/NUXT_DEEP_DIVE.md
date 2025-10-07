# Nuxt.js Deep Dive for Next.js Developers

## Table of Contents

1. [Nuxt.js Fundamentals](#nuxtjs-fundamentals)
2. [Vue.js vs React Patterns](#vuejs-vs-react-patterns)
3. [State Management with Pinia](#state-management-with-pinia)
4. [Data Fetching and SSR](#data-fetching-and-ssr)
5. [Composables and Reusable Logic](#composables-and-reusable-logic)
6. [Performance and Optimization](#performance-and-optimization)

## Nuxt.js Fundamentals

### 1. File-based Routing (Similar to Next.js)

Nuxt.js uses a similar file-based routing system to Next.js, but with some key differences:

```
Next.js Structure          →    Nuxt.js Structure
pages/                     →    app/pages/
├── index.tsx             →    ├── index.vue
├── about.tsx             →    ├── about.vue
├── dashboard/            →    ├── dashboard/
│   ├── index.tsx         →    │   ├── index.vue
│   └── settings.tsx      →    │   └── settings.vue
└── [id].tsx              →    └── [id].vue
```

**Key Differences:**

- Uses `.vue` files instead of `.tsx`
- Automatic component imports (no need for explicit imports)
- Built-in layouts system
- Different data fetching patterns

### 2. App Directory Structure (Nuxt 4)

```typescript
// nuxt.config.ts
export default defineNuxtConfig({
  // Nuxt 4 app directory structure
  // Pages are automatically discovered from app/pages/
  // Components are automatically discovered from app/components/
  // Composables are automatically discovered from app/composables/
  // No need for explicit configuration for these in Nuxt 4
});
```

**Directory Structure:**

```
app/
├── pages/           # File-based routing
├── components/      # Vue components (auto-imported)
├── composables/     # Reusable logic (auto-imported)
├── layouts/         # Layout components
├── stores/          # Pinia stores
├── utils/           # Utility functions
└── app.vue          # Root component
```

### 3. Auto-imports and Conventions

Nuxt.js automatically imports many functions and components:

```vue
<!-- No need to import these - they're auto-imported -->
<template>
  <div>
    <h1>{{ title }}</h1>
    <button @click="handleClick">Click me</button>
  </div>
</template>

<script setup lang="ts">
// These are auto-imported:
// - ref, reactive, computed, watch
// - useRouter, useRoute, navigateTo
// - $fetch, useFetch, useLazyFetch
// - definePageMeta, defineProps, defineEmits

const title = ref("Hello Nuxt!");
const router = useRouter();
const route = useRoute();

const handleClick = () => {
  navigateTo("/dashboard");
};
</script>
```

## Vue.js vs React Patterns

### 1. Component Structure

**React Component (Next.js)**

```typescript
// components/SurveyCard.tsx
import { useState } from "react";
import { Survey } from "@/types";

interface SurveyCardProps {
  survey: Survey;
  onEdit: (id: string) => void;
  onDelete: (id: string) => void;
}

export function SurveyCard({ survey, onEdit, onDelete }: SurveyCardProps) {
  const [isLoading, setIsLoading] = useState(false);

  const handleEdit = () => {
    setIsLoading(true);
    onEdit(survey.id);
    setIsLoading(false);
  };

  return (
    <div className="survey-card">
      <h3>{survey.title}</h3>
      <p>{survey.description}</p>
      <div className="actions">
        <button onClick={handleEdit} disabled={isLoading}>
          Edit
        </button>
        <button onClick={() => onDelete(survey.id)}>Delete</button>
      </div>
    </div>
  );
}
```

**Vue Component (Nuxt.js)**

```vue
<!-- app/components/SurveyCard.vue -->
<template>
  <div class="survey-card">
    <h3>{{ survey.title }}</h3>
    <p>{{ survey.description }}</p>
    <div class="actions">
      <button @click="handleEdit" :disabled="isLoading">Edit</button>
      <button @click="handleDelete">Delete</button>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  survey: Survey;
}

interface Emits {
  edit: [id: string];
  delete: [id: string];
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const isLoading = ref(false);

const handleEdit = () => {
  isLoading.value = true;
  emit("edit", props.survey.id);
  isLoading.value = false;
};

const handleDelete = () => {
  emit("delete", props.survey.id);
};
</script>

<style scoped>
.survey-card {
  @apply bg-white rounded-lg shadow-md p-6;
}
</style>
```

### 2. State Management Patterns

**React with useState/useReducer**

```typescript
// hooks/useSurveys.ts
import { useState, useEffect } from "react";

export function useSurveys() {
  const [surveys, setSurveys] = useState<Survey[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const fetchSurveys = async () => {
    setIsLoading(true);
    try {
      const response = await fetch("/api/surveys");
      const data = await response.json();
      setSurveys(data);
    } catch (err) {
      setError("Failed to fetch surveys");
    } finally {
      setIsLoading(false);
    }
  };

  const addSurvey = (survey: Survey) => {
    setSurveys((prev) => [...prev, survey]);
  };

  const updateSurvey = (id: string, updates: Partial<Survey>) => {
    setSurveys((prev) =>
      prev.map((s) => (s.id === id ? { ...s, ...updates } : s))
    );
  };

  const deleteSurvey = (id: string) => {
    setSurveys((prev) => prev.filter((s) => s.id !== id));
  };

  useEffect(() => {
    fetchSurveys();
  }, []);

  return {
    surveys,
    isLoading,
    error,
    fetchSurveys,
    addSurvey,
    updateSurvey,
    deleteSurvey,
  };
}
```

**Vue with Pinia**

```typescript
// app/stores/surveys.ts
export const useSurveysStore = defineStore("surveys", () => {
  const surveys = ref<Survey[]>([]);
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  const fetchSurveys = async () => {
    isLoading.value = true;
    try {
      const response = await $fetch("/api/v1/surveys", {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
        },
      });
      surveys.value = response.data;
    } catch (err) {
      error.value = "Failed to fetch surveys";
    } finally {
      isLoading.value = false;
    }
  };

  const addSurvey = (survey: Survey) => {
    surveys.value.push(survey);
  };

  const updateSurvey = (id: string, updates: Partial<Survey>) => {
    const index = surveys.value.findIndex((s) => s.id === id);
    if (index !== -1) {
      surveys.value[index] = { ...surveys.value[index], ...updates };
    }
  };

  const deleteSurvey = (id: string) => {
    surveys.value = surveys.value.filter((s) => s.id !== id);
  };

  return {
    surveys,
    isLoading,
    error,
    fetchSurveys,
    addSurvey,
    updateSurvey,
    deleteSurvey,
  };
});
```

### 3. Lifecycle Hooks Comparison

**React Lifecycle**

```typescript
// React component lifecycle
function MyComponent() {
  const [data, setData] = useState(null);

  // Component mount
  useEffect(() => {
    fetchData();
  }, []);

  // Component update
  useEffect(() => {
    updateData();
  }, [data]);

  // Component unmount
  useEffect(() => {
    return () => {
      cleanup();
    };
  }, []);

  return <div>{data}</div>;
}
```

**Vue Lifecycle**

```vue
<!-- Vue component lifecycle -->
<template>
  <div>{{ data }}</div>
</template>

<script setup lang="ts">
const data = ref(null);

// Component mount
onMounted(() => {
  fetchData();
});

// Component update
watch(data, () => {
  updateData();
});

// Component unmount
onUnmounted(() => {
  cleanup();
});
</script>
```

## State Management with Pinia

### 1. Pinia Store Structure

```typescript
// app/stores/auth.ts
export interface User {
  id: string;
  name: string;
  email: string;
}

export interface AuthState {
  user: User | null;
  token: string | null;
  isLoggedIn: boolean;
  isLoading: boolean;
}

export const useAuthStore = defineStore("auth", () => {
  // State
  const user = ref<User | null>(null);
  const token = ref<string | null>(null);
  const isLoading = ref(false);

  // Computed
  const isLoggedIn = computed(() => !!user.value && !!token.value);

  // Actions
  const login = async (email: string, password: string) => {
    isLoading.value = true;
    try {
      const response = await $fetch("/api/v1/login", {
        method: "POST",
        body: { email, password },
      });

      user.value = response.data.user;
      token.value = response.data.token;

      // Persist to localStorage
      localStorage.setItem("auth_token", response.data.token);
      localStorage.setItem("user", JSON.stringify(response.data.user));
    } catch (error: any) {
      throw new Error(error.data?.message || "Login failed");
    } finally {
      isLoading.value = false;
    }
  };

  const logout = async () => {
    try {
      await $fetch("/api/v1/logout", {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token.value}`,
        },
      });
    } catch (error) {
      console.error("Logout API call failed:", error);
    } finally {
      // Clear local state regardless of API response
      user.value = null;
      token.value = null;
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user");
    }
  };

  const initializeAuth = () => {
    const storedToken = localStorage.getItem("auth_token");
    const storedUser = localStorage.getItem("user");

    if (storedToken && storedUser) {
      token.value = storedToken;
      user.value = JSON.parse(storedUser);
    }
  };

  return {
    // State
    user: readonly(user),
    token: readonly(token),
    isLoading: readonly(isLoading),

    // Computed
    isLoggedIn,

    // Actions
    login,
    logout,
    initializeAuth,
  };
});
```

### 2. Store Composition and Modularity

```typescript
// app/stores/surveys.ts
export const useSurveysStore = defineStore("surveys", () => {
  const authStore = useAuthStore();

  // State
  const surveys = ref<Survey[]>([]);
  const currentSurvey = ref<Survey | null>(null);
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  // Computed
  const publishedSurveys = computed(() =>
    surveys.value.filter((s) => s.status === "published")
  );

  const userSurveys = computed(() =>
    surveys.value.filter((s) => s.user_id === authStore.user?.id)
  );

  // Actions
  const fetchSurveys = async () => {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await $fetch("/api/v1/surveys", {
        headers: {
          Authorization: `Bearer ${authStore.token}`,
        },
      });
      surveys.value = response.data;
    } catch (err: any) {
      error.value = err.data?.message || "Failed to fetch surveys";
    } finally {
      isLoading.value = false;
    }
  };

  const fetchSurvey = async (id: string) => {
    isLoading.value = true;
    try {
      const response = await $fetch(`/api/v1/surveys/${id}`, {
        headers: {
          Authorization: `Bearer ${authStore.token}`,
        },
      });
      currentSurvey.value = response.data;
    } catch (err: any) {
      error.value = err.data?.message || "Failed to fetch survey";
    } finally {
      isLoading.value = false;
    }
  };

  const createSurvey = async (surveyData: CreateSurveyData) => {
    try {
      const response = await $fetch("/api/v1/surveys", {
        method: "POST",
        headers: {
          Authorization: `Bearer ${authStore.token}`,
        },
        body: surveyData,
      });

      surveys.value.push(response.data);
      return response.data;
    } catch (err: any) {
      throw new Error(err.data?.message || "Failed to create survey");
    }
  };

  return {
    // State
    surveys: readonly(surveys),
    currentSurvey: readonly(currentSurvey),
    isLoading: readonly(isLoading),
    error: readonly(error),

    // Computed
    publishedSurveys,
    userSurveys,

    // Actions
    fetchSurveys,
    fetchSurvey,
    createSurvey,
  };
});
```

### 3. Store Persistence

```typescript
// plugins/persisted-state.client.ts
import { createPersistedState } from "pinia-plugin-persistedstate";

export default defineNuxtPlugin((nuxtApp) => {
  nuxtApp.$pinia.use(
    createPersistedState({
      storage: localStorage,
      key: (id) => `__persisted_${id}`,
    })
  );
});

// Store with persistence
export const useAuthStore = defineStore(
  "auth",
  () => {
    // ... store implementation
  },
  {
    persist: {
      storage: persistedState.localStorage,
      paths: ["user", "token"], // Only persist these fields
    },
  }
);
```

## Data Fetching and SSR

### 1. Server-Side Rendering Patterns

**Next.js SSR**

```typescript
// pages/surveys/[id].tsx
export async function getServerSideProps(context: GetServerSidePropsContext) {
  const { id } = context.params!;

  try {
    const survey = await prisma.survey.findUnique({
      where: { id: id as string },
      include: { questions: true },
    });

    if (!survey) {
      return { notFound: true };
    }

    return {
      props: { survey },
    };
  } catch (error) {
    return { notFound: true };
  }
}

export default function SurveyPage({ survey }: { survey: Survey }) {
  return (
    <div>
      <h1>{survey.title}</h1>
      <p>{survey.description}</p>
    </div>
  );
}
```

**Nuxt.js SSR**

```vue
<!-- app/pages/surveys/[id].vue -->
<template>
  <div>
    <h1>{{ survey.title }}</h1>
    <p>{{ survey.description }}</p>
  </div>
</template>

<script setup lang="ts">
const route = useRoute();
const { data: survey, error } = await $fetch(
  `/api/v1/surveys/${route.params.id}`
);

if (error) {
  throw createError({
    statusCode: 404,
    statusMessage: "Survey not found",
  });
}
</script>
```

### 2. Data Fetching Composables

```typescript
// app/composables/useSurveys.ts
export const useSurveys = () => {
  const surveysStore = useSurveysStore();

  const fetchSurveys = async () => {
    await surveysStore.fetchSurveys();
  };

  const fetchSurvey = async (id: string) => {
    await surveysStore.fetchSurvey(id);
  };

  const createSurvey = async (data: CreateSurveyData) => {
    return await surveysStore.createSurvey(data);
  };

  return {
    surveys: computed(() => surveysStore.surveys),
    currentSurvey: computed(() => surveysStore.currentSurvey),
    isLoading: computed(() => surveysStore.isLoading),
    error: computed(() => surveysStore.error),
    fetchSurveys,
    fetchSurvey,
    createSurvey,
  };
};

// Usage in component
export default defineComponent({
  setup() {
    const { surveys, isLoading, fetchSurveys } = useSurveys();

    onMounted(() => {
      fetchSurveys();
    });

    return {
      surveys,
      isLoading,
    };
  },
});
```

### 3. Client-Side Data Fetching

```typescript
// app/composables/useApi.ts
export const useApi = () => {
  const authStore = useAuthStore();

  const api = $fetch.create({
    baseURL: "http://127.0.0.1:8000/api/v1",
    headers: {
      "Content-Type": "application/json",
    },
    onRequest({ request, options }) {
      if (authStore.token) {
        options.headers = {
          ...options.headers,
          Authorization: `Bearer ${authStore.token}`,
        };
      }
    },
    onResponseError({ response }) {
      if (response.status === 401) {
        authStore.logout();
        navigateTo("/login");
      }
    },
  });

  return { api };
};

// Usage
const { api } = useApi();

const surveys = await api("/surveys");
const survey = await api(`/surveys/${id}`);
```

## Composables and Reusable Logic

### 1. Form Handling Composable

```typescript
// app/composables/useForm.ts
export function useForm<T extends Record<string, any>>(
  initialValues: T,
  validationSchema?: any
) {
  const form = reactive({ ...initialValues });
  const errors = ref<Partial<Record<keyof T, string>>>({});
  const isSubmitting = ref(false);

  const validate = () => {
    errors.value = {};

    if (!validationSchema) return true;

    try {
      validationSchema.parse(form);
      return true;
    } catch (error: any) {
      error.errors.forEach((err: any) => {
        errors.value[err.path[0] as keyof T] = err.message;
      });
      return false;
    }
  };

  const submit = async (submitFn: (data: T) => Promise<any>) => {
    if (!validate()) return;

    isSubmitting.value = true;
    try {
      const result = await submitFn(form);
      return result;
    } catch (error: any) {
      if (error.data?.errors) {
        errors.value = error.data.errors;
      }
      throw error;
    } finally {
      isSubmitting.value = false;
    }
  };

  const reset = () => {
    Object.assign(form, initialValues);
    errors.value = {};
  };

  return {
    form,
    errors: readonly(errors),
    isSubmitting: readonly(isSubmitting),
    validate,
    submit,
    reset,
  };
}

// Usage
const { form, errors, isSubmitting, submit } = useForm({
  title: "",
  description: "",
  status: "draft",
});

const handleSubmit = async () => {
  try {
    await submit(async (data) => {
      return await surveysStore.createSurvey(data);
    });
    navigateTo("/dashboard");
  } catch (error) {
    console.error("Form submission failed:", error);
  }
};
```

### 2. Modal Management Composable

```typescript
// app/composables/useModal.ts
export function useModal() {
  const isOpen = ref(false);
  const data = ref<any>(null);

  const open = (modalData?: any) => {
    data.value = modalData;
    isOpen.value = true;
  };

  const close = () => {
    isOpen.value = false;
    data.value = null;
  };

  return {
    isOpen: readonly(isOpen),
    data: readonly(data),
    open,
    close,
  };
}

// Usage in component
const deleteModal = useModal();
const editModal = useModal();

const handleDelete = (survey: Survey) => {
  deleteModal.open(survey);
};

const handleEdit = (survey: Survey) => {
  editModal.open(survey);
};
```

### 3. Local Storage Composable

```typescript
// app/composables/useLocalStorage.ts
export function useLocalStorage<T>(key: string, defaultValue: T) {
  const storedValue = localStorage.getItem(key);
  const initialValue = storedValue ? JSON.parse(storedValue) : defaultValue;

  const value = ref<T>(initialValue);

  watch(
    value,
    (newValue) => {
      localStorage.setItem(key, JSON.stringify(newValue));
    },
    { deep: true }
  );

  return value;
}

// Usage
const theme = useLocalStorage("theme", "light");
const userPreferences = useLocalStorage("userPreferences", {
  language: "en",
  notifications: true,
});
```

## Performance and Optimization

### 1. Lazy Loading and Code Splitting

```vue
<!-- Lazy load components -->
<template>
  <div>
    <LazySurveyForm v-if="showForm" />
    <LazySurveyList v-if="showList" />
  </div>
</template>

<script setup lang="ts">
const showForm = ref(false);
const showList = ref(true);

// Lazy load heavy components
const HeavyComponent = defineAsyncComponent(
  () => import("~/components/HeavyComponent.vue")
);
</script>
```

### 2. Virtual Scrolling for Large Lists

```vue
<!-- app/components/VirtualSurveyList.vue -->
<template>
  <div class="virtual-list" ref="containerRef">
    <div v-for="survey in visibleSurveys" :key="survey.id" class="survey-item">
      <SurveyCard :survey="survey" />
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  surveys: Survey[];
  itemHeight?: number;
  containerHeight?: number;
}

const props = withDefaults(defineProps<Props>(), {
  itemHeight: 200,
  containerHeight: 600,
});

const containerRef = ref<HTMLElement>();
const scrollTop = ref(0);

const visibleStart = computed(() =>
  Math.floor(scrollTop.value / props.itemHeight)
);

const visibleEnd = computed(() =>
  Math.min(
    visibleStart.value +
      Math.ceil(props.containerHeight / props.itemHeight) +
      1,
    props.surveys.length
  )
);

const visibleSurveys = computed(() =>
  props.surveys.slice(visibleStart.value, visibleEnd.value)
);

const handleScroll = (event: Event) => {
  const target = event.target as HTMLElement;
  scrollTop.value = target.scrollTop;
};

onMounted(() => {
  containerRef.value?.addEventListener("scroll", handleScroll);
});

onUnmounted(() => {
  containerRef.value?.removeEventListener("scroll", handleScroll);
});
</script>
```

### 3. Caching and Memoization

```typescript
// app/composables/useCachedData.ts
export function useCachedData<T>(
  key: string,
  fetcher: () => Promise<T>,
  ttl: number = 300000 // 5 minutes
) {
  const cache = useLocalStorage(`cache_${key}`, {
    data: null as T | null,
    timestamp: 0,
  });

  const isExpired = computed(() => Date.now() - cache.value.timestamp > ttl);

  const fetchData = async () => {
    if (cache.value.data && !isExpired.value) {
      return cache.value.data;
    }

    const data = await fetcher();
    cache.value = {
      data,
      timestamp: Date.now(),
    };

    return data;
  };

  return {
    data: computed(() => cache.value.data),
    fetchData,
    clearCache: () => {
      cache.value = { data: null, timestamp: 0 };
    },
  };
}

// Usage
const { data: surveys, fetchData } = useCachedData(
  "surveys",
  () => surveysStore.fetchSurveys(),
  600000 // 10 minutes
);
```

This comprehensive guide covers the essential Nuxt.js and Vue.js concepts that will help you transition from Next.js development. The patterns shown here are based on the actual project structure and represent production-ready practices for building modern web applications with Nuxt.js.
