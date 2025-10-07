# Laravel + Nuxt.js Integration Patterns

## Table of Contents

1. [API Integration Patterns](#api-integration-patterns)
2. [Authentication Flow](#authentication-flow)
3. [Error Handling Strategies](#error-handling-strategies)
4. [Real-time Features](#real-time-features)
5. [File Upload and Management](#file-upload-and-management)
6. [Deployment and DevOps](#deployment-and-devops)

## API Integration Patterns

### 1. Centralized API Client

**Frontend API Client (Nuxt.js)**

```typescript
// app/utils/api.ts
export const api = $fetch.create({
  baseURL: "http://127.0.0.1:8000/api/v1",
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
  onRequest({ request, options }) {
    const token = localStorage.getItem("auth_token");
    if (token) {
      options.headers = {
        ...options.headers,
        Authorization: `Bearer ${token}`,
      };
    }
  },
  onRequestError({ request, error }) {
    console.error("Request error:", error);
  },
  onResponse({ response }) {
    // Handle successful responses
    if (response.status === 201) {
      // Show success notification
      console.log("Resource created successfully");
    }
  },
  onResponseError({ response }) {
    if (response.status === 401) {
      // Redirect to login
      navigateTo("/login");
    } else if (response.status === 422) {
      // Handle validation errors
      console.error("Validation failed:", response._data);
    } else if (response.status >= 500) {
      // Handle server errors
      console.error("Server error:", response._data);
    }
  },
});

// Type-safe API methods
export const surveysApi = {
  getAll: (params?: any) => api("/surveys", { query: params }),
  getById: (id: string) => api(`/surveys/${id}`),
  create: (data: CreateSurveyData) =>
    api("/surveys", { method: "POST", body: data }),
  update: (id: string, data: UpdateSurveyData) =>
    api(`/surveys/${id}`, { method: "PUT", body: data }),
  delete: (id: string) => api(`/surveys/${id}`, { method: "DELETE" }),
  publish: (id: string) => api(`/surveys/${id}/publish`, { method: "POST" }),
  getQuestions: (id: string) => api(`/surveys/${id}/questions`),
  createQuestion: (surveyId: string, data: CreateQuestionData) =>
    api(`/surveys/${surveyId}/questions`, { method: "POST", body: data }),
  submitResponse: (surveyId: string, data: ResponseData) =>
    api(`/surveys/${surveyId}/responses`, { method: "POST", body: data }),
};
```

**Backend API Structure (Laravel)**

```php
// routes/api.php
Route::prefix('v1')->group(function () {
    // Public routes
    Route::get('/surveys/{survey}/public', [SurveyController::class, 'showPublic']);
    Route::post('/surveys/{survey}/responses', [ResponseController::class, 'store']);

    // Authentication routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        // Survey management
        Route::apiResource('surveys', SurveyController::class);
        Route::post('/surveys/{survey}/publish', [SurveyController::class, 'publish']);

        // Question management
        Route::get('/surveys/{survey}/questions', [QuestionController::class, 'index']);
        Route::post('/surveys/{survey}/questions', [QuestionController::class, 'store']);
        Route::apiResource('questions', QuestionController::class)->except(['index', 'store']);

        // Response management
        Route::get('/surveys/{survey}/responses', [ResponseController::class, 'index']);
    });
});
```

### 2. Request/Response Interceptors

**Frontend Interceptors**

```typescript
// app/plugins/api.client.ts
export default defineNuxtPlugin(() => {
  // Request interceptor
  $fetch.create({
    onRequest({ request, options }) {
      // Add CSRF token if needed
      const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
      if (csrfToken) {
        options.headers = {
          ...options.headers,
          "X-CSRF-TOKEN": csrfToken,
        };
      }

      // Add request timestamp for caching
      options.query = {
        ...options.query,
        _t: Date.now(),
      };
    },

    onResponse({ response }) {
      // Handle rate limiting
      if (response.headers.get("X-RateLimit-Remaining") === "0") {
        console.warn("Rate limit reached");
      }
    },

    onResponseError({ response }) {
      // Global error handling
      if (response.status === 429) {
        // Rate limited
        const retryAfter = response.headers.get("Retry-After");
        console.warn(`Rate limited. Retry after ${retryAfter} seconds`);
      }
    },
  });
});
```

**Backend Middleware**

```php
// app/Http/Middleware/ApiResponseMiddleware.php
class ApiResponseMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Add CORS headers
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

        // Add rate limiting headers
        $response->headers->set('X-RateLimit-Limit', '60');
        $response->headers->set('X-RateLimit-Remaining', '59');

        return $response;
    }
}

// app/Http/Middleware/LogApiRequests.php
class LogApiRequests
{
    public function handle($request, Closure $next)
    {
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $duration = ($endTime - $startTime) * 1000; // Convert to milliseconds

        Log::info('API Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status' => $response->getStatusCode(),
            'duration' => round($duration, 2) . 'ms',
            'user_id' => auth()->id(),
        ]);

        return $response;
    }
}
```

## Authentication Flow

### 1. Complete Authentication Implementation

**Frontend Authentication Store**

```typescript
// app/stores/auth.ts
export const useAuthStore = defineStore("auth", () => {
  const user = ref<User | null>(null);
  const token = ref<string | null>(null);
  const isLoading = ref(false);

  const isLoggedIn = computed(() => !!user.value && !!token.value);

  const login = async (credentials: LoginCredentials) => {
    isLoading.value = true;
    try {
      const response = await $fetch("/api/v1/login", {
        method: "POST",
        body: credentials,
      });

      user.value = response.data.user;
      token.value = response.data.token;

      // Persist to localStorage
      localStorage.setItem("auth_token", response.data.token);
      localStorage.setItem("user", JSON.stringify(response.data.user));

      // Set default headers for future requests
      await navigateTo("/dashboard");
    } catch (error: any) {
      throw new Error(error.data?.message || "Login failed");
    } finally {
      isLoading.value = false;
    }
  };

  const register = async (userData: RegisterData) => {
    isLoading.value = true;
    try {
      const response = await $fetch("/api/v1/register", {
        method: "POST",
        body: userData,
      });

      user.value = response.data.user;
      token.value = response.data.token;

      localStorage.setItem("auth_token", response.data.token);
      localStorage.setItem("user", JSON.stringify(response.data.user));

      await navigateTo("/dashboard");
    } catch (error: any) {
      throw new Error(error.data?.message || "Registration failed");
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
      await navigateTo("/login");
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

  const refreshToken = async () => {
    try {
      const response = await $fetch("/api/v1/refresh", {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token.value}`,
        },
      });

      token.value = response.data.token;
      localStorage.setItem("auth_token", response.data.token);
    } catch (error) {
      // If refresh fails, logout user
      await logout();
    }
  };

  return {
    user: readonly(user),
    token: readonly(token),
    isLoading: readonly(isLoading),
    isLoggedIn,
    login,
    register,
    logout,
    initializeAuth,
    refreshToken,
  };
});
```

**Backend Authentication Controller**

```php
// app/Http/Controllers/Api/V1/AuthController.php
class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => UserResource::make($user),
                'token' => $token,
            ],
            'message' => 'User registered successfully'
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => UserResource::make($user),
                'token' => $token,
            ],
            'message' => 'Login successful'
        ]);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function user(): JsonResponse
    {
        return response()->json([
            'data' => UserResource::make(auth()->user())
        ]);
    }

    public function refresh(): JsonResponse
    {
        $user = auth()->user();
        $user->currentAccessToken()->delete();

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
            ],
            'message' => 'Token refreshed successfully'
        ]);
    }
}
```

### 2. Route Protection

**Frontend Route Middleware**

```typescript
// middleware/auth.ts
export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore();

  if (!authStore.isLoggedIn) {
    return navigateTo("/login");
  }
});

// middleware/guest.ts
export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore();

  if (authStore.isLoggedIn) {
    return navigateTo("/dashboard");
  }
});
```

**Backend Route Protection**

```php
// app/Http/Middleware/EnsureApiTokenIsValid.php
class EnsureApiTokenIsValid
{
    public function handle($request, Closure $next)
    {
        if (!$request->user() || !$request->user()->currentAccessToken()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        return $next($request);
    }
}

// Usage in routes
Route::middleware(['auth:sanctum', EnsureApiTokenIsValid::class])->group(function () {
    // Protected routes
});
```

## Error Handling Strategies

### 1. Global Error Handling

**Frontend Error Handling**

```typescript
// app/plugins/error-handler.client.ts
export default defineNuxtPlugin(() => {
  // Global error handler
  const handleError = (error: any) => {
    console.error("Global error:", error);

    // Show user-friendly error message
    if (error.statusCode === 404) {
      throw createError({
        statusCode: 404,
        statusMessage: "Page not found",
      });
    } else if (error.statusCode === 500) {
      throw createError({
        statusCode: 500,
        statusMessage: "Internal server error",
      });
    }
  };

  // Vue error handler
  const app = getCurrentInstance()?.appContext.app;
  if (app) {
    app.config.errorHandler = handleError;
  }

  // Unhandled promise rejection handler
  window.addEventListener("unhandledrejection", (event) => {
    handleError(event.reason);
  });
});

// app/composables/useErrorHandler.ts
export const useErrorHandler = () => {
  const error = ref<string | null>(null);
  const isLoading = ref(false);

  const handleAsync = async <T>(
    asyncFn: () => Promise<T>
  ): Promise<T | null> => {
    try {
      isLoading.value = true;
      error.value = null;
      return await asyncFn();
    } catch (err: any) {
      error.value = err.message || "An error occurred";
      console.error("Async operation failed:", err);
      return null;
    } finally {
      isLoading.value = false;
    }
  };

  const clearError = () => {
    error.value = null;
  };

  return {
    error: readonly(error),
    isLoading: readonly(isLoading),
    handleAsync,
    clearError,
  };
};
```

**Backend Error Handling**

```php
// app/Exceptions/Handler.php
class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $exception);
        }

        return parent::render($request, $exception);
    }

    private function handleApiException($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $exception->errors(),
            ], 422);
        }

        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'message' => 'Resource not found',
            ], 404);
        }

        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        // Log the exception
        Log::error('API Exception', [
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'request' => $request->all(),
        ]);

        return response()->json([
            'message' => 'Internal server error',
        ], 500);
    }
}
```

### 2. Form Validation Error Handling

**Frontend Form Validation**

```vue
<!-- app/components/SurveyForm.vue -->
<template>
  <form @submit.prevent="handleSubmit">
    <div class="form-group">
      <label for="title">Title</label>
      <input
        id="title"
        v-model="form.title"
        type="text"
        :class="{ error: errors.title }"
      />
      <span v-if="errors.title" class="error-message">
        {{ errors.title }}
      </span>
    </div>

    <div class="form-group">
      <label for="description">Description</label>
      <textarea
        id="description"
        v-model="form.description"
        :class="{ error: errors.description }"
      ></textarea>
      <span v-if="errors.description" class="error-message">
        {{ errors.description }}
      </span>
    </div>

    <button type="submit" :disabled="isSubmitting">
      {{ isSubmitting ? "Creating..." : "Create Survey" }}
    </button>
  </form>
</template>

<script setup lang="ts">
const { form, errors, isSubmitting, submit } = useForm({
  title: "",
  description: "",
  status: "draft",
});

const handleSubmit = async () => {
  try {
    await submit(async (data) => {
      return await surveysApi.create(data);
    });

    // Success - redirect or show success message
    await navigateTo("/dashboard");
  } catch (error: any) {
    // Error handling is done in the useForm composable
    console.error("Form submission failed:", error);
  }
};
</script>
```

**Backend Validation**

```php
// app/Http/Requests/StoreSurveyRequest.php
class StoreSurveyRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string|max:255|unique:surveys,title',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:draft,published,closed',
            'settings' => 'nullable|array',
            'settings.allow_anonymous' => 'boolean',
            'settings.max_responses' => 'nullable|integer|min:1|max:10000',
            'settings.expires_at' => 'nullable|date|after:now',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Survey title is required.',
            'title.unique' => 'A survey with this title already exists.',
            'title.max' => 'Survey title cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'status.in' => 'Status must be one of: draft, published, closed.',
            'settings.max_responses.max' => 'Maximum responses cannot exceed 10,000.',
            'settings.expires_at.after' => 'Expiration date must be in the future.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->title),
            'user_id' => auth()->id(),
        ]);
    }
}
```

## Real-time Features

### 1. WebSocket Integration

**Backend WebSocket Setup**

```php
// Install Laravel WebSockets or Pusher
composer require pusher/pusher-php-server

// config/broadcasting.php
'pusher' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'useTLS' => true,
    ],
],

// Event for real-time notifications
class SurveyResponseReceived implements ShouldBroadcast
{
    public function __construct(
        public Survey $survey,
        public Response $response
    ) {}

    public function broadcastOn()
    {
        return new PrivateChannel('survey.' . $this->survey->id);
    }

    public function broadcastWith()
    {
        return [
            'survey_id' => $this->survey->id,
            'response_id' => $this->response->id,
            'respondent_id' => $this->response->respondent_id,
            'created_at' => $this->response->created_at->toISOString(),
        ];
    }
}

// Controller method to trigger event
public function store(Request $request, int $surveyId)
{
    // ... store response logic ...

    // Broadcast event
    broadcast(new SurveyResponseReceived($survey, $response));

    return response()->json(['message' => 'Response submitted successfully']);
}
```

**Frontend WebSocket Integration**

```typescript
// app/plugins/pusher.client.ts
import Pusher from "pusher-js";

export default defineNuxtPlugin(() => {
  const pusher = new Pusher(process.env.NUXT_PUBLIC_PUSHER_KEY!, {
    cluster: process.env.NUXT_PUBLIC_PUSHER_CLUSTER!,
    authEndpoint: "/api/v1/broadcasting/auth",
    auth: {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
      },
    },
  });

  return {
    provide: {
      pusher,
    },
  };
});

// app/composables/useRealtime.ts
export const useRealtime = () => {
  const { $pusher } = useNuxtApp();

  const subscribeToSurvey = (
    surveyId: string,
    callback: (data: any) => void
  ) => {
    const channel = $pusher.subscribe(`private-survey.${surveyId}`);

    channel.bind("SurveyResponseReceived", callback);

    return () => {
      $pusher.unsubscribe(`private-survey.${surveyId}`);
    };
  };

  const subscribeToUserNotifications = (
    userId: string,
    callback: (data: any) => void
  ) => {
    const channel = $pusher.subscribe(`private-user.${userId}`);

    channel.bind("NotificationReceived", callback);

    return () => {
      $pusher.unsubscribe(`private-user.${userId}`);
    };
  };

  return {
    subscribeToSurvey,
    subscribeToUserNotifications,
  };
};
```

### 2. Real-time Survey Updates

```vue
<!-- app/pages/surveys/[id]/responses.vue -->
<template>
  <div>
    <h1>Survey Responses</h1>
    <div class="stats">
      <div class="stat">
        <span class="label">Total Responses:</span>
        <span class="value">{{ totalResponses }}</span>
      </div>
      <div class="stat">
        <span class="label">Today:</span>
        <span class="value">{{ todayResponses }}</span>
      </div>
    </div>

    <div class="responses-list">
      <div
        v-for="response in responses"
        :key="response.id"
        class="response-item"
      >
        <div class="response-header">
          <span class="respondent-id">{{ response.respondent_id }}</span>
          <span class="timestamp">{{ formatDate(response.created_at) }}</span>
        </div>
        <div class="response-content">
          <!-- Response details -->
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const route = useRoute();
const surveyId = route.params.id as string;

const responses = ref<Response[]>([]);
const totalResponses = ref(0);
const todayResponses = ref(0);

const { subscribeToSurvey } = useRealtime();

// Fetch initial data
const fetchResponses = async () => {
  const data = await surveysApi.getResponses(surveyId);
  responses.value = data.responses;
  totalResponses.value = data.total;
  todayResponses.value = data.today;
};

// Subscribe to real-time updates
onMounted(() => {
  fetchResponses();

  const unsubscribe = subscribeToSurvey(surveyId, (data) => {
    // Add new response to the list
    responses.value.unshift({
      id: data.response_id,
      respondent_id: data.respondent_id,
      created_at: data.created_at,
      // ... other fields
    });

    totalResponses.value++;

    // Check if it's today's response
    const responseDate = new Date(data.created_at);
    const today = new Date();
    if (responseDate.toDateString() === today.toDateString()) {
      todayResponses.value++;
    }
  });

  onUnmounted(() => {
    unsubscribe();
  });
});
</script>
```

## File Upload and Management

### 1. File Upload Implementation

**Frontend File Upload**

```vue
<!-- app/components/FileUpload.vue -->
<template>
  <div class="file-upload">
    <input
      ref="fileInput"
      type="file"
      :accept="accept"
      :multiple="multiple"
      @change="handleFileSelect"
      class="hidden"
    />

    <div
      @click="triggerFileSelect"
      @dragover.prevent
      @drop.prevent="handleDrop"
      class="upload-area"
      :class="{ dragover: isDragOver }"
    >
      <div v-if="!files.length" class="upload-placeholder">
        <Icon name="upload" class="upload-icon" />
        <p>Click to upload or drag and drop</p>
        <p class="text-sm text-gray-500">{{ acceptText }}</p>
      </div>

      <div v-else class="file-list">
        <div v-for="(file, index) in files" :key="index" class="file-item">
          <span>{{ file.name }}</span>
          <button @click="removeFile(index)" class="remove-btn">
            <Icon name="x" />
          </button>
        </div>
      </div>
    </div>

    <div v-if="isUploading" class="upload-progress">
      <div class="progress-bar" :style="{ width: `${uploadProgress}%` }"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  accept?: string;
  multiple?: boolean;
  maxSize?: number; // in MB
}

const props = withDefaults(defineProps<Props>(), {
  accept: "*/*",
  multiple: false,
  maxSize: 10,
});

const emit = defineEmits<{
  files: [files: File[]];
  upload: [files: File[]];
}>();

const fileInput = ref<HTMLInputElement>();
const files = ref<File[]>([]);
const isDragOver = ref(false);
const isUploading = ref(false);
const uploadProgress = ref(0);

const acceptText = computed(() => {
  if (props.accept === "image/*") return "Images only";
  if (props.accept === ".pdf") return "PDF files only";
  return "Any file type";
});

const triggerFileSelect = () => {
  fileInput.value?.click();
};

const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files) {
    processFiles(Array.from(target.files));
  }
};

const handleDrop = (event: DragEvent) => {
  isDragOver.value = false;
  if (event.dataTransfer?.files) {
    processFiles(Array.from(event.dataTransfer.files));
  }
};

const processFiles = (newFiles: File[]) => {
  const validFiles = newFiles.filter((file) => {
    if (file.size > props.maxSize * 1024 * 1024) {
      console.error(`File ${file.name} is too large`);
      return false;
    }
    return true;
  });

  if (props.multiple) {
    files.value.push(...validFiles);
  } else {
    files.value = validFiles;
  }

  emit("files", files.value);
};

const removeFile = (index: number) => {
  files.value.splice(index, 1);
  emit("files", files.value);
};

const uploadFiles = async () => {
  if (!files.value.length) return;

  isUploading.value = true;
  uploadProgress.value = 0;

  try {
    const formData = new FormData();
    files.value.forEach((file) => {
      formData.append("files[]", file);
    });

    const response = await $fetch("/api/v1/upload", {
      method: "POST",
      body: formData,
      onUploadProgress: (progress) => {
        uploadProgress.value = Math.round(
          (progress.loaded / progress.total) * 100
        );
      },
    });

    emit("upload", files.value);
    files.value = [];
  } catch (error) {
    console.error("Upload failed:", error);
  } finally {
    isUploading.value = false;
    uploadProgress.value = 0;
  }
};
</script>
```

**Backend File Upload**

```php
// app/Http/Controllers/Api/V1/FileController.php
class FileController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240', // 10MB max
        ]);

        $uploadedFiles = [];

        foreach ($request->file('files') as $file) {
            $path = $file->store('uploads', 'public');

            $uploadedFile = File::create([
                'original_name' => $file->getClientOriginalName(),
                'filename' => $file->hashName(),
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'user_id' => auth()->id(),
            ]);

            $uploadedFiles[] = [
                'id' => $uploadedFile->id,
                'original_name' => $uploadedFile->original_name,
                'url' => Storage::url($path),
                'size' => $uploadedFile->size,
                'mime_type' => $uploadedFile->mime_type,
            ];
        }

        return response()->json([
            'data' => $uploadedFiles,
            'message' => 'Files uploaded successfully'
        ]);
    }

    public function delete(File $file)
    {
        // Check if user owns the file
        if ($file->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete file from storage
        Storage::disk('public')->delete($file->path);

        // Delete database record
        $file->delete();

        return response()->json(['message' => 'File deleted successfully']);
    }
}
```

## Deployment and DevOps

### 1. Environment Configuration

**Frontend Environment Variables**

```typescript
// nuxt.config.ts
export default defineNuxtConfig({
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://127.0.0.1:8000',
      pusherKey: process.env.NUXT_PUBLIC_PUSHER_KEY,
      pusherCluster: process.env.NUXT_PUBLIC_PUSHER_CLUSTER,
    }
  }
});

// .env
NUXT_PUBLIC_API_BASE=https://api.yourdomain.com
NUXT_PUBLIC_PUSHER_KEY=your_pusher_key
NUXT_PUBLIC_PUSHER_CLUSTER=your_cluster
```

**Backend Environment Variables**

```php
// .env
APP_NAME="Survey MVP"
APP_ENV=production
APP_KEY=base64:your_app_key
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=survey_mvp
DB_USERNAME=your_username
DB_PASSWORD=your_password

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
```

### 2. Docker Configuration

**Docker Compose**

```yaml
# docker-compose.yml
version: "3.8"

services:
  app:
    build:
      context: ./backend
      dockerfile: Dockerfile
    container_name: survey_backend
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./backend:/var/www
      - ./backend/storage:/var/www/storage
    ports:
      - "8000:8000"
    environment:
      - APP_ENV=production
      - DB_HOST=db
      - DB_DATABASE=survey_mvp
      - DB_USERNAME=root
      - DB_PASSWORD=password
    depends_on:
      - db
      - redis

  frontend:
    build:
      context: ./frontend
      dockerfile: Dockerfile
    container_name: survey_frontend
    restart: unless-stopped
    ports:
      - "3000:3000"
    environment:
      - NUXT_PUBLIC_API_BASE=http://app:8000

  db:
    image: mysql:8.0
    container_name: survey_db
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: survey_mvp
      MYSQL_ROOT_PASSWORD: password
    volumes:
      - db_data:/var/lib/mysql
    ports:
      - "3306:3306"

  redis:
    image: redis:alpine
    container_name: survey_redis
    restart: unless-stopped
    ports:
      - "6379:6379"

volumes:
  db_data:
```

**Backend Dockerfile**

```dockerfile
# backend/Dockerfile
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Change current user to www
USER www-data

# Expose port 8000 and start php-fpm server
EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=8000
```

**Frontend Dockerfile**

```dockerfile
# frontend/Dockerfile
FROM node:18-alpine

# Set working directory
WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm ci --only=production

# Copy source code
COPY . .

# Build the application
RUN npm run build

# Expose port 3000
EXPOSE 3000

# Start the application
CMD ["npm", "start"]
```

This comprehensive integration guide covers the essential patterns for building robust Laravel + Nuxt.js applications. The examples are based on the actual project structure and represent production-ready practices for seamless frontend-backend integration.
