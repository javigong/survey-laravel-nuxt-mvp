# Frontend Documentation

## Overview

The Survey MVP frontend is built with Nuxt.js 4, Vue 3, TypeScript, and Tailwind CSS. It provides a modern, responsive interface for creating and managing surveys with a comprehensive question builder.

## Technology Stack

- **Framework**: Nuxt.js 4
- **Frontend**: Vue 3 with Composition API
- **Language**: TypeScript
- **Styling**: Tailwind CSS
- **State Management**: Pinia
- **HTTP Client**: $fetch (ofetch)
- **Build Tool**: Vite

## Project Structure

```
frontend/
├── app/                          # Application code (Nuxt 4 convention)
│   ├── components/               # Reusable Vue components
│   │   ├── QuestionEditor.vue   # Question creation/editing modal
│   │   └── QuestionPreview.vue  # Question preview component
│   ├── composables/             # Vue composables
│   │   ├── useAuth.ts          # Authentication logic
│   │   └── useSurvey.ts        # Survey management logic
│   ├── layouts/                 # Page layouts
│   │   └── dashboard.vue       # Dashboard layout
│   ├── middleware/              # Route middleware
│   │   └── auth.ts             # Authentication middleware
│   ├── pages/                   # File-based routing
│   │   ├── dashboard/          # Dashboard pages
│   │   │   └── surveys/        # Survey management pages
│   │   │       ├── index.vue   # Survey list
│   │   │       ├── create.vue  # Create survey
│   │   │       └── [id]/       # Dynamic survey pages
│   │   │           └── edit.vue # Edit survey
│   │   ├── login.vue           # Login page
│   │   ├── register.vue        # Registration page
│   │   └── question-builder.vue # Standalone question builder
│   ├── stores/                  # Pinia stores
│   │   ├── auth.ts             # Authentication state
│   │   └── survey.ts           # Survey data state
│   └── utils/                   # Utility functions
├── assets/                      # Static assets
│   └── css/                    # Global styles
├── nuxt.config.ts              # Nuxt configuration
├── package.json                # Dependencies
└── tailwind.config.js          # Tailwind configuration
```

## Components

### QuestionEditor Component

**File**: `app/components/QuestionEditor.vue`

A modal component for creating and editing survey questions with dynamic form fields based on question type.

#### Props

```typescript
interface Props {
  question: Question; // Question object to edit (optional for new questions)
}
```

#### Events

```typescript
interface Events {
  save: (questionData: Partial<Question>) => void; // Emitted when question is saved
  cancel: () => void; // Emitted when editing is cancelled
}
```

#### Features

- **Dynamic Form**: Adapts to different question types
- **Options Management**: Add/remove options for multiple choice questions
- **Validation**: Real-time form validation
- **Type Safety**: Full TypeScript support

#### Usage Example

```vue
<template>
  <QuestionEditor
    :question="editingQuestion"
    @save="handleSaveQuestion"
    @cancel="closeEditor"
  />
</template>
```

#### Question Types Supported

| Type                       | Display Name    | Options Required | Validation               |
| -------------------------- | --------------- | ---------------- | ------------------------ |
| `text_short`               | Short Text      | No               | Required field           |
| `text_long`                | Long Text       | No               | Required field           |
| `multiple_choice_single`   | Multiple Choice | Yes              | Required field + options |
| `multiple_choice_multiple` | Checkboxes      | Yes              | Required field + options |
| `rating_scale`             | Rating Scale    | No               | Min/max values           |
| `yes_no`                   | Yes/No          | No               | Required field           |
| `dropdown`                 | Dropdown        | Yes              | Required field + options |
| `date`                     | Date            | No               | Required field           |
| `time`                     | Time            | No               | Required field           |
| `datetime`                 | Date & Time     | No               | Required field           |
| `file_upload`              | File Upload     | No               | File type validation     |

### QuestionPreview Component

**File**: `app/components/QuestionPreview.vue`

A read-only component that displays how a question will appear to survey respondents.

#### Props

```typescript
interface Props {
  question: Question; // Question object to preview
}
```

#### Features

- **Type-specific Rendering**: Different input types based on question type
- **Validation States**: Shows required field indicators
- **Options Display**: Renders options for multiple choice questions
- **Disabled State**: All inputs are disabled for preview

#### Usage Example

```vue
<template>
  <QuestionPreview :question="question" />
</template>
```

## Composables

### useAuth Composable

**File**: `app/composables/useAuth.ts`

Handles all authentication-related operations.

#### Interface

```typescript
interface LoginCredentials {
  email: string;
  password: string;
}

interface RegisterData {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}

export const useAuth = () => {
  const login = async (credentials: LoginCredentials): Promise<void>;
  const logout = async (): Promise<void>;
  const register = async (userData: RegisterData): Promise<void>;

  return { login, logout, register };
};
```

#### Usage Example

```typescript
const { login, logout, register } = useAuth();

// Login user
await login({ email: "user@example.com", password: "password123" });

// Register new user
await register({
  name: "John Doe",
  email: "john@example.com",
  password: "password123",
  password_confirmation: "password123",
});

// Logout user
await logout();
```

### useSurvey Composable

**File**: `app/composables/useSurvey.ts`

Handles all survey and question management operations.

#### Interface

```typescript
interface CreateSurveyData {
  title: string;
  description?: string;
  status?: 'draft' | 'published' | 'closed';
}

interface CreateQuestionData {
  survey_id: number;
  title: string;
  description?: string;
  type: Question['type'];
  options?: string[];
  validation_rules?: Record<string, any>;
  is_required?: boolean;
  order?: number;
}

interface SurveyFilters {
  title?: string;
  status?: string;
}

export const useSurvey = () => {
  // Survey operations
  const fetchSurveys = async (filters?: SurveyFilters): Promise<{ data: Survey[]; links: any; meta: any }>;
  const createSurvey = async (data: CreateSurveyData): Promise<Survey>;
  const getSurvey = async (id: number): Promise<Survey>;
  const updateSurvey = async (id: number, data: Partial<CreateSurveyData>): Promise<Survey>;
  const deleteSurvey = async (id: number): Promise<void>;

  // Question operations
  const fetchQuestions = async (surveyId: number): Promise<Question[]>;
  const createQuestion = async (data: CreateQuestionData): Promise<Question>;
  const updateQuestion = async (id: number, data: Partial<CreateQuestionData>): Promise<Question>;
  const deleteQuestion = async (id: number): Promise<void>;
  const reorderQuestions = async (surveyId: number, questionIds: number[]): Promise<void>;

  return {
    fetchSurveys, createSurvey, getSurvey, updateSurvey, deleteSurvey,
    fetchQuestions, createQuestion, updateQuestion, deleteQuestion, reorderQuestions
  };
};
```

#### Usage Example

```typescript
const { createSurvey, fetchSurveys, createQuestion } = useSurvey();

// Create a new survey
const survey = await createSurvey({
  title: "Customer Feedback",
  description: "Help us improve our service",
  status: "draft",
});

// Fetch surveys with filters
const { data: surveys } = await fetchSurveys({
  title: "Customer",
  status: "published",
});

// Create a question
const question = await createQuestion({
  survey_id: survey.id,
  title: "How satisfied are you?",
  type: "rating_scale",
  is_required: true,
  order: 0,
});
```

## State Management

### Auth Store (Pinia)

**File**: `app/stores/auth.ts`

Manages authentication state and user information.

#### State Interface

```typescript
interface AuthState {
  user: User | null;
  token: string | null;
  isLoggedIn: boolean;
}

interface User {
  id: number;
  name: string;
  email: string;
  created_at: string;
  updated_at: string;
}
```

#### Actions

```typescript
interface AuthStore {
  // State
  user: Ref<User | null>;
  token: Ref<string | null>;
  isLoggedIn: Ref<boolean>;

  // Actions
  setUser(user: User): void;
  setToken(token: string): void;
  clearAuth(): void;
  initializeAuth(): void;
}
```

#### Usage Example

```typescript
const authStore = useAuthStore();

// Check if user is logged in
if (authStore.isLoggedIn) {
  console.log("User:", authStore.user?.name);
}

// Set user data
authStore.setUser({
  id: 1,
  name: "John Doe",
  email: "john@example.com",
  created_at: "2025-01-07T10:00:00.000000Z",
  updated_at: "2025-01-07T10:00:00.000000Z",
});
```

### Survey Store (Pinia)

**File**: `app/stores/survey.ts`

Manages survey and question data state.

#### State Interface

```typescript
interface SurveyState {
  surveys: Survey[];
  currentSurvey: Survey | null;
  questions: Question[];
}

interface Survey {
  id: number;
  title: string;
  description: string;
  status: "draft" | "published" | "closed";
  user_id: number;
  question_count: number;
  response_count: number;
  questions?: Question[];
  created_at: string;
  updated_at: string;
}

interface Question {
  id: number;
  survey_id: number;
  title: string;
  description: string | null;
  type: QuestionType;
  type_display: string;
  options: string[] | null;
  validation_rules: Record<string, any> | null;
  is_required: boolean;
  order: number;
  created_at: string;
  updated_at: string;
}

type QuestionType =
  | "text_short"
  | "text_long"
  | "multiple_choice_single"
  | "multiple_choice_multiple"
  | "rating_scale"
  | "yes_no"
  | "dropdown"
  | "date"
  | "time"
  | "datetime"
  | "file_upload";
```

## Pages

### Dashboard Layout

**File**: `app/layouts/dashboard.vue`

Provides the main layout for authenticated users with navigation and user controls.

#### Features

- **Navigation Bar**: Logo, user info, and logout button
- **Responsive Design**: Mobile-friendly navigation
- **Dark Mode Support**: Tailwind dark mode classes
- **Slot Content**: Renders child page content

#### Usage

```vue
<template>
  <div>
    <!-- Page content -->
  </div>
</template>

<script setup>
definePageMeta({
  layout: "dashboard",
});
</script>
```

### Survey List Page

**File**: `app/pages/dashboard/surveys/index.vue`

Displays a paginated list of user's surveys with search and filter capabilities.

#### Features

- **Survey Grid**: Card-based layout showing survey information
- **Search**: Filter surveys by title
- **Status Filter**: Filter by draft, published, or closed
- **Actions**: Edit, delete, and view survey options
- **Question Count**: Shows number of questions per survey
- **Response Count**: Shows number of responses per survey

#### Data Display

Each survey card shows:

- Survey title and description
- Status badge (draft/published/closed)
- Question count with type icons
- Response count
- Creation date
- Action buttons

### Survey Create Page

**File**: `app/pages/dashboard/surveys/create.vue`

Provides a comprehensive interface for creating new surveys with the question builder.

#### Features

- **Survey Details Form**: Title, description, and status
- **Question Builder**: Add questions with different types
- **Real-time Preview**: See how questions will appear
- **Save Options**: Save as draft or publish immediately

### Survey Edit Page

**File**: `app/pages/dashboard/surveys/[id]/edit.vue`

Allows editing existing surveys with full question management capabilities.

#### Features

- **Survey Details**: Edit title, description, and status
- **Question Management**: Add, edit, duplicate, and delete questions
- **Question Reordering**: Drag and drop to reorder questions
- **Live Preview**: Real-time preview of all questions
- **Bulk Operations**: Duplicate and delete multiple questions

### Question Builder Page

**File**: `app/pages/question-builder.vue`

Standalone page for the question builder interface, useful for testing and development.

#### Features

- **Full Question Builder**: Complete question creation interface
- **All Question Types**: Support for all 11 question types
- **Preview Mode**: See how questions will appear to respondents
- **Save Functionality**: Save surveys with questions

## Middleware

### Authentication Middleware

**File**: `app/middleware/auth.ts`

Protects routes that require authentication by checking user login status.

#### Features

- **Route Protection**: Redirects unauthenticated users to login
- **Token Validation**: Checks for valid authentication token
- **Client-side Only**: Runs only on client-side to avoid SSR issues

#### Usage

```typescript
// Applied automatically to protected routes
// No manual configuration needed
```

## Styling

### Tailwind CSS Configuration

**File**: `tailwind.config.js`

Custom Tailwind configuration with project-specific settings.

#### Features

- **Dark Mode**: Support for dark/light theme switching
- **Custom Colors**: Brand-specific color palette
- **Responsive Design**: Mobile-first approach
- **Component Classes**: Reusable utility classes

#### Custom Classes

```css
/* Custom component classes */
.btn-primary {
  @apply px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500;
}

.card {
  @apply bg-white dark:bg-gray-800 shadow rounded-lg p-6;
}

.input {
  @apply w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white;
}
```

### Global Styles

**File**: `assets/css/main.css`

Global CSS styles and Tailwind imports.

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom global styles */
@layer base {
  html {
    font-family: "Inter", system-ui, sans-serif;
  }
}

@layer components {
  /* Component styles */
}
```

## Configuration

### Nuxt Configuration

**File**: `nuxt.config.ts`

Main Nuxt.js configuration file with all project settings.

#### Key Configuration

```typescript
export default defineNuxtConfig({
  devtools: { enabled: true },
  css: ["~/assets/css/main.css"],
  modules: ["@nuxtjs/tailwindcss", "@pinia/nuxt"],
  runtimeConfig: {
    public: {
      apiBase: "http://127.0.0.1:8000",
    },
  },
  components: {
    dirs: ["./app/components/ui"],
  },
  imports: {
    dirs: ["./app/composables", "./app/stores"],
  },
  experimental: {
    payloadExtraction: false,
  },
});
```

## Development

### Local Development

1. **Install Dependencies**:

   ```bash
   npm install
   ```

2. **Start Development Server**:

   ```bash
   npm run dev
   ```

3. **Build for Production**:

   ```bash
   npm run build
   ```

4. **Preview Production Build**:
   ```bash
   npm run preview
   ```

### Code Standards

#### TypeScript

- Use strict TypeScript configuration
- Define interfaces for all data structures
- Use type assertions sparingly
- Prefer composition API with `<script setup>`

#### Vue 3

- Use Composition API
- Define props with TypeScript interfaces
- Use `defineEmits` for component events
- Prefer `ref` and `reactive` for state management

#### Styling

- Use Tailwind utility classes
- Create custom components for repeated patterns
- Use CSS custom properties for theming
- Follow mobile-first responsive design

### Testing

#### Unit Testing

```bash
# Run unit tests
npm run test:unit

# Run tests with coverage
npm run test:coverage
```

#### E2E Testing

```bash
# Run E2E tests
npm run test:e2e

# Run E2E tests in headed mode
npm run test:e2e:headed
```

### Performance Optimization

#### Code Splitting

- Automatic code splitting by Nuxt.js
- Lazy loading of components
- Dynamic imports for heavy components

#### Bundle Optimization

- Tree shaking for unused code
- Minification in production
- Gzip compression

#### Image Optimization

- Automatic image optimization
- WebP format support
- Lazy loading for images

## Deployment

### Build Process

1. **Install Dependencies**:

   ```bash
   npm ci --only=production
   ```

2. **Build Application**:

   ```bash
   npm run build
   ```

3. **Deploy Static Files**:
   - Upload `.output/public` directory to web server
   - Configure server for SPA routing

### Environment Variables

```bash
# API Configuration
NUXT_PUBLIC_API_BASE=http://127.0.0.1:8000

# Build Configuration
NODE_ENV=production
```

### Server Configuration

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/.output/public;
    index index.html;

    # SPA routing
    location / {
        try_files $uri $uri/ /index.html;
    }

    # Static assets
    location /_nuxt/ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

## Troubleshooting

### Common Issues

#### Hydration Mismatch

**Problem**: Server and client rendering differences

**Solution**: Use `ClientOnly` component for client-specific code

```vue
<template>
  <ClientOnly>
    <div>Client-only content</div>
  </ClientOnly>
</template>
```

#### Import Resolution

**Problem**: Module not found errors

**Solution**: Check `nuxt.config.ts` imports configuration

```typescript
imports: {
  dirs: ["./app/composables", "./app/stores"];
}
```

#### TypeScript Errors

**Problem**: Type errors in components

**Solution**: Ensure proper type definitions and interfaces

```typescript
interface Props {
  question: Question;
}

defineProps<Props>();
```

### Debug Mode

Enable debug mode for development:

```typescript
// nuxt.config.ts
export default defineNuxtConfig({
  debug: true,
  devtools: { enabled: true },
});
```

---

**Last Updated**: January 7, 2025  
**Frontend Version**: 1.0.0
