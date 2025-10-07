# Framework Transition Guide: Next.js to Laravel + Nuxt.js

## Table of Contents

1. [Documentation Overview](#documentation-overview)
2. [Key Benefits of This Documentation](#key-benefits-of-this-documentation)
3. [How to Use This Documentation](#how-to-use-this-documentation)
4. [Overview](#overview)
5. [Laravel Backend Concepts](#laravel-backend-concepts)
6. [Nuxt.js Frontend Concepts](#nuxtjs-frontend-concepts)
7. [Integration Patterns](#integration-patterns)
8. [Migration Guide](#migration-guide)
9. [Project Structure Analysis](#project-structure-analysis)
10. [Best Practices](#best-practices)

## Documentation Overview

This comprehensive documentation suite is specifically designed for Next.js developers transitioning to the Laravel + Nuxt.js stack. The documentation consists of **5 detailed guides** that work together to provide complete coverage of the ecosystem:

### 📚 Complete Documentation Suite

1. **[Framework Transition Guide](./FRAMEWORK_TRANSITION.md)** - _This document_ - Your starting point
2. **[Laravel Deep Dive](./LARAVEL_DEEP_DIVE.md)** - Master the backend concepts
3. **[Nuxt.js Deep Dive](./NUXT_DEEP_DIVE.md)** - Master the frontend concepts
4. **[Integration Patterns](./INTEGRATION_PATTERNS.md)** - Connect everything together
5. **[Project Analysis](./PROJECT_ANALYSIS.md)** - Real-world implementation example

### 🎯 Documentation Structure

Each guide builds upon the previous one, ensuring a smooth learning curve:

```
Framework Transition Guide (30 min)
    ↓
Laravel Deep Dive (45 min)
    ↓
Nuxt.js Deep Dive (45 min)
    ↓
Integration Patterns (60 min)
    ↓
Project Analysis (30 min)
```

**Total estimated reading time: 3.5 hours** for complete mastery

### 📖 What Each Guide Covers

| Guide                    | Focus Area             | Key Learning Outcomes                                            |
| ------------------------ | ---------------------- | ---------------------------------------------------------------- |
| **Framework Transition** | Big picture comparison | Understand differences, similarities, and migration strategies   |
| **Laravel Deep Dive**    | Backend mastery        | Eloquent ORM, Sanctum auth, API design, performance optimization |
| **Nuxt.js Deep Dive**    | Frontend mastery       | Vue.js patterns, Pinia state management, SSR/SSG, composables    |
| **Integration Patterns** | Full-stack connection  | API integration, auth flow, error handling, real-time features   |
| **Project Analysis**     | Real-world example     | Complete project breakdown, actual code patterns, best practices |

## Key Benefits of This Documentation

### 🚀 **For Next.js Developers**

- **Familiar Patterns** - Every Laravel concept is explained by comparing to Next.js equivalents
- **Side-by-Side Examples** - See Next.js code alongside Laravel/Nuxt.js implementations
- **Migration Strategies** - Step-by-step guide to transition your existing projects
- **Real Project Analysis** - Based on an actual survey application with working code

### 📚 **Comprehensive Coverage**

- **Backend**: Laravel 12, Eloquent ORM, Sanctum authentication, API design patterns
- **Frontend**: Nuxt.js 4, Vue.js 3, Pinia state management, SSR/SSG strategies
- **Integration**: API communication, error handling, real-time features, file uploads
- **DevOps**: Deployment, Docker, environment configuration, CI/CD patterns

### 🏭 **Production-Ready**

- All examples are based on actual project structure and working code
- Includes comprehensive error handling, validation, and security patterns
- Covers performance optimization and scalability best practices
- Provides working code you can copy, adapt, and use immediately

### 🎓 **Learning-Focused**

- **Progressive Learning** - Each guide builds upon the previous one
- **Code Examples** - 100+ working examples with explanations
- **Comparison Tables** - 15+ side-by-side comparisons between frameworks
- **Best Practices** - 50+ production-ready patterns and techniques

## How to Use This Documentation

### 🎯 **For Complete Beginners to Laravel + Nuxt.js**

**Recommended Path:**

1. **Start here** - Read this Framework Transition Guide completely
2. **Backend first** - Study Laravel Deep Dive to understand the backend
3. **Frontend second** - Master Nuxt.js Deep Dive for frontend concepts
4. **Integration** - Learn Integration Patterns to connect everything
5. **Real-world** - Analyze Project Analysis to see everything in action

**Time Investment:** 3.5 hours total for complete mastery

### 🔄 **For Developers with Some Experience**

**Quick Reference Path:**

1. **Skip to relevant sections** - Use the table of contents to find specific topics
2. **Focus on differences** - Pay attention to comparison tables and side-by-side examples
3. **Study integration patterns** - Focus on how Laravel and Nuxt.js work together
4. **Analyze the project** - Use the real project as a reference implementation

**Time Investment:** 1-2 hours for targeted learning

### 🛠️ **For Active Development**

**Practical Usage:**

1. **Keep this guide open** - Use as a reference while coding
2. **Copy code examples** - Adapt the provided patterns to your project
3. **Follow best practices** - Implement the recommended patterns and techniques
4. **Reference the project** - Use the real project analysis as a template

**Time Investment:** Ongoing reference during development

### 📋 **Learning Checklist**

Use this checklist to track your progress:

- [ ] **Framework Transition Guide** - Understand the big picture
- [ ] **Laravel Deep Dive** - Master backend concepts
- [ ] **Nuxt.js Deep Dive** - Master frontend concepts
- [ ] **Integration Patterns** - Learn full-stack integration
- [ ] **Project Analysis** - Study real-world implementation
- [ ] **Set up development environment** - Get both frameworks running
- [ ] **Build a sample project** - Apply what you've learned
- [ ] **Deploy to production** - Use the deployment patterns provided

### 💡 **Pro Tips for Maximum Learning**

1. **Read with code open** - Have the actual project code open while reading
2. **Try examples** - Don't just read, actually run the code examples
3. **Take notes** - Jot down key differences and patterns as you learn
4. **Build something** - Create a small project to practice the concepts
5. **Ask questions** - Use the patterns as a starting point for deeper exploration

---

## Overview

This guide helps Next.js developers transition to the Laravel + Nuxt.js stack. Both frameworks share similar concepts but have different approaches to full-stack development.

### Key Differences Summary

| Aspect               | Next.js                        | Laravel + Nuxt.js                 |
| -------------------- | ------------------------------ | --------------------------------- |
| **Backend**          | API Routes, Server Actions     | Laravel Controllers, Eloquent ORM |
| **Frontend**         | React Components               | Vue.js Components                 |
| **State Management** | Context API, Zustand, Redux    | Pinia (Vuex successor)            |
| **Routing**          | File-based routing             | File-based routing (similar)      |
| **SSR/SSG**          | Built-in                       | Built-in with different patterns  |
| **Database**         | Prisma, Drizzle                | Eloquent ORM                      |
| **Authentication**   | NextAuth.js, Clerk             | Laravel Sanctum                   |
| **Styling**          | CSS Modules, Styled Components | Tailwind CSS, Scoped CSS          |

---

## Laravel Backend Concepts

### 1. MVC Architecture

Laravel follows the Model-View-Controller (MVC) pattern:

```php
// Model (app/Models/User.php)
class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }
}

// Controller (app/Http/Controllers/Api/V1/SurveyController.php)
class SurveyController extends Controller
{
    public function index()
    {
        return SurveyResource::collection(
            auth()->user()->surveys()->paginate()
        );
    }
}

// API Resource (app/Http/Resources/SurveyResource.php)
class SurveyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
```

### 2. Eloquent ORM vs Prisma

**Prisma (Next.js)**

```typescript
// schema.prisma
model User {
  id        String   @id @default(cuid())
  email     String   @unique
  name      String?
  surveys   Survey[]
}

// Usage
const user = await prisma.user.findUnique({
  where: { email: 'user@example.com' },
  include: { surveys: true }
});
```

**Eloquent (Laravel)**

```php
// Migration
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->timestamps();
});

// Model
class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }
}

// Usage
$user = User::where('email', 'user@example.com')
    ->with('surveys')
    ->first();
```

### 3. API Routes vs Next.js API Routes

**Next.js API Routes**

```typescript
// pages/api/surveys/index.ts
export default async function handler(
  req: NextApiRequest,
  res: NextApiResponse
) {
  if (req.method === "GET") {
    const surveys = await prisma.survey.findMany({
      where: { userId: req.user.id },
    });
    res.json(surveys);
  }
}
```

**Laravel API Routes**

```php
// routes/api.php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('surveys', SurveyController::class);
});

// Controller handles all HTTP methods automatically
class SurveyController extends Controller
{
    public function index() { /* GET /api/surveys */ }
    public function store() { /* POST /api/surveys */ }
    public function show() { /* GET /api/surveys/{id} */ }
    public function update() { /* PUT /api/surveys/{id} */ }
    public function destroy() { /* DELETE /api/surveys/{id} */ }
}
```

### 4. Authentication: NextAuth.js vs Laravel Sanctum

**NextAuth.js**

```typescript
// pages/api/auth/[...nextauth].ts
export default NextAuth({
  providers: [
    CredentialsProvider({
      async authorize(credentials) {
        const user = await prisma.user.findUnique({
          where: { email: credentials.email },
        });
        if (user && bcrypt.compare(credentials.password, user.password)) {
          return user;
        }
        return null;
      },
    }),
  ],
  callbacks: {
    async jwt({ token, user }) {
      if (user) token.id = user.id;
      return token;
    },
  },
});
```

**Laravel Sanctum**

```php
// AuthController.php
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
        ]
    ]);
}
```

### 5. Middleware and Request Validation

**Laravel Request Validation**

```php
// app/Http/Requests/StoreSurveyRequest.php
class StoreSurveyRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:draft,published,closed',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Survey title is required.',
            'status.in' => 'Status must be draft, published, or closed.',
        ];
    }
}
```

---

## Nuxt.js Frontend Concepts

### 1. File-based Routing (Similar to Next.js)

**Next.js Pages**

```
pages/
  index.tsx
  dashboard/
    surveys/
      index.tsx
      [id].tsx
      create.tsx
  api/
    surveys/
      index.ts
```

**Nuxt.js Pages**

```
app/pages/
  index.vue
  dashboard/
    surveys/
      index.vue
      [id].vue
      create.vue
```

### 2. Components and Composables

**Next.js with React**

```typescript
// components/SurveyCard.tsx
interface SurveyCardProps {
  survey: Survey;
  onEdit: (id: string) => void;
}

export function SurveyCard({ survey, onEdit }: SurveyCardProps) {
  const [isLoading, setIsLoading] = useState(false);

  return (
    <div className="survey-card">
      <h3>{survey.title}</h3>
      <button onClick={() => onEdit(survey.id)}>Edit</button>
    </div>
  );
}
```

**Nuxt.js with Vue**

```vue
<!-- app/components/SurveyCard.vue -->
<template>
  <div class="survey-card">
    <h3>{{ survey.title }}</h3>
    <button @click="handleEdit">Edit</button>
  </div>
</template>

<script setup lang="ts">
interface Props {
  survey: Survey;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  edit: [id: string];
}>();

const isLoading = ref(false);

const handleEdit = () => {
  emit("edit", props.survey.id);
};
</script>
```

### 3. State Management: Context API vs Pinia

**Next.js with Context API**

```typescript
// contexts/AuthContext.tsx
const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  const login = async (email: string, password: string) => {
    const response = await fetch("/api/auth/login", {
      method: "POST",
      body: JSON.stringify({ email, password }),
    });
    const data = await response.json();
    setUser(data.user);
  };

  return (
    <AuthContext.Provider value={{ user, login, isLoading }}>
      {children}
    </AuthContext.Provider>
  );
}
```

**Nuxt.js with Pinia**

```typescript
// app/stores/auth.ts
export const useAuthStore = defineStore("auth", () => {
  const user = ref<User | null>(null);
  const isLoading = ref(false);

  const login = async (email: string, password: string) => {
    isLoading.value = true;
    try {
      const response = await $fetch("/api/v1/login", {
        method: "POST",
        body: { email, password },
      });
      user.value = response.data.user;
      // Store token
      localStorage.setItem("auth_token", response.data.token);
    } finally {
      isLoading.value = false;
    }
  };

  return { user, isLoading, login };
});
```

### 4. Data Fetching: SWR/React Query vs Nuxt's $fetch

**Next.js with SWR**

```typescript
// hooks/useSurveys.ts
import useSWR from "swr";

const fetcher = (url: string) => fetch(url).then((res) => res.json());

export function useSurveys() {
  const { data, error, mutate } = useSWR("/api/surveys", fetcher);

  return {
    surveys: data,
    isLoading: !error && !data,
    isError: error,
    mutate,
  };
}
```

**Nuxt.js with $fetch**

```typescript
// app/composables/useSurveys.ts
export const useSurveys = () => {
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

  return { surveys, isLoading, error, fetchSurveys };
};
```

### 5. Server-Side Rendering (SSR)

**Next.js SSR**

```typescript
// pages/surveys/[id].tsx
export async function getServerSideProps(context: GetServerSidePropsContext) {
  const { id } = context.params!;

  const survey = await prisma.survey.findUnique({
    where: { id: id as string },
    include: { questions: true },
  });

  return {
    props: { survey },
  };
}

export default function SurveyPage({ survey }: { survey: Survey }) {
  return <div>{survey.title}</div>;
}
```

**Nuxt.js SSR**

```vue
<!-- app/pages/surveys/[id].vue -->
<template>
  <div>{{ survey.title }}</div>
</template>

<script setup lang="ts">
const route = useRoute();
const { data: survey } = await $fetch(`/api/v1/surveys/${route.params.id}`, {
  headers: {
    Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
  },
});
</script>
```

---

## Integration Patterns

### 1. API Communication

**Frontend API Client (Nuxt.js)**

```typescript
// app/utils/api.ts
export const api = $fetch.create({
  baseURL: "http://127.0.0.1:8000/api/v1",
  headers: {
    "Content-Type": "application/json",
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
  onResponseError({ response }) {
    if (response.status === 401) {
      // Redirect to login
      navigateTo("/login");
    }
  },
});
```

**Backend API Response (Laravel)**

```php
// SurveyController.php
public function index()
{
    $surveys = auth()->user()->surveys()->paginate(15);

    return response()->json([
        'data' => SurveyResource::collection($surveys),
        'meta' => [
            'current_page' => $surveys->currentPage(),
            'total' => $surveys->total(),
            'per_page' => $surveys->perPage(),
        ]
    ]);
}
```

### 2. Error Handling

**Frontend Error Handling**

```vue
<!-- app/pages/surveys/index.vue -->
<template>
  <div>
    <div v-if="error" class="error-message">
      {{ error }}
    </div>
    <div v-else-if="isLoading">Loading surveys...</div>
    <div v-else>
      <SurveyCard v-for="survey in surveys" :key="survey.id" :survey="survey" />
    </div>
  </div>
</template>

<script setup lang="ts">
const { surveys, isLoading, error, fetchSurveys } = useSurveys();

onMounted(() => {
  fetchSurveys();
});
</script>
```

**Backend Error Handling**

```php
// app/Http/Controllers/Api/V1/SurveyController.php
public function store(StoreSurveyRequest $request)
{
    try {
        $survey = auth()->user()->surveys()->create($request->validated());

        return response()->json([
            'data' => SurveyResource::make($survey),
            'message' => 'Survey created successfully'
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to create survey',
            'error' => $e->getMessage()
        ], 500);
    }
}
```

---

## Migration Guide

### 1. Project Structure Migration

**From Next.js to Laravel + Nuxt.js**

```
Next.js Structure          →    Laravel + Nuxt.js Structure
├── pages/                 →    ├── frontend/app/pages/
├── components/            →    ├── frontend/app/components/
├── lib/                   →    ├── frontend/app/utils/
├── hooks/                 →    ├── frontend/app/composables/
├── context/               →    ├── frontend/app/stores/
├── api/                   →    ├── backend/app/Http/Controllers/
├── prisma/                →    ├── backend/database/migrations/
└── public/                →    ├── frontend/public/
                                └── backend/public/
```

### 2. Step-by-Step Migration Process

1. **Set up Laravel Backend**

   ```bash
   composer create-project laravel/laravel backend
   cd backend
   composer require laravel/sanctum
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   ```

2. **Set up Nuxt.js Frontend**

   ```bash
   npx nuxi@latest init frontend
   cd frontend
   npm install @pinia/nuxt @nuxtjs/tailwindcss
   ```

3. **Migrate Database Schema**

   - Convert Prisma schema to Laravel migrations
   - Set up Eloquent models with relationships

4. **Migrate API Routes**

   - Convert Next.js API routes to Laravel controllers
   - Implement proper validation and error handling

5. **Migrate Frontend Components**
   - Convert React components to Vue components
   - Migrate state management from Context API to Pinia

### 3. Common Migration Patterns

**API Route Migration**

```typescript
// Next.js API Route
export default async function handler(
  req: NextApiRequest,
  res: NextApiResponse
) {
  if (req.method === "GET") {
    const surveys = await prisma.survey.findMany();
    res.json(surveys);
  }
}
```

```php
// Laravel Controller
class SurveyController extends Controller
{
    public function index()
    {
        return SurveyResource::collection(Survey::all());
    }
}
```

**Component Migration**

```typescript
// React Component
function SurveyList({ surveys }: { surveys: Survey[] }) {
  return (
    <div>
      {surveys.map((survey) => (
        <div key={survey.id}>{survey.title}</div>
      ))}
    </div>
  );
}
```

```vue
<!-- Vue Component -->
<template>
  <div>
    <div v-for="survey in surveys" :key="survey.id">
      {{ survey.title }}
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  surveys: Survey[];
}

defineProps<Props>();
</script>
```

---

## Project Structure Analysis

Based on the current project, here are the key patterns:

### Backend Structure (Laravel)

```
backend/
├── app/
│   ├── Http/Controllers/Api/V1/    # API Controllers
│   ├── Models/                     # Eloquent Models
│   ├── Http/Resources/             # API Resources
│   └── Http/Requests/              # Form Requests
├── database/
│   ├── migrations/                 # Database migrations
│   └── seeders/                    # Database seeders
├── routes/
│   └── api.php                     # API routes
└── config/                         # Configuration files
```

### Frontend Structure (Nuxt.js)

```
frontend/
├── app/
│   ├── pages/                      # File-based routing
│   ├── components/                 # Vue components
│   ├── composables/                # Reusable logic
│   ├── stores/                     # Pinia stores
│   └── layouts/                    # Layout components
├── assets/                         # Static assets
└── nuxt.config.ts                  # Nuxt configuration
```

### Key Features Implemented

1. **Authentication System**

   - Laravel Sanctum for token-based auth
   - Pinia store for state management
   - Middleware for route protection

2. **Survey Management**

   - CRUD operations for surveys
   - Question management
   - Response collection

3. **API Design**
   - RESTful API structure
   - Proper HTTP status codes
   - Resource transformations

---

## Best Practices

### 1. Laravel Backend Best Practices

- **Use Form Requests** for validation
- **Implement API Resources** for consistent response format
- **Use Eloquent Relationships** instead of manual joins
- **Apply Middleware** for authentication and rate limiting
- **Follow RESTful conventions** for API endpoints

### 2. Nuxt.js Frontend Best Practices

- **Use Composables** for reusable logic
- **Implement Error Boundaries** for better error handling
- **Use Pinia** for complex state management
- **Leverage SSR** for SEO optimization
- **Follow Vue 3 Composition API** patterns

### 3. Integration Best Practices

- **Consistent Error Handling** across frontend and backend
- **Proper CORS Configuration** for cross-origin requests
- **Token-based Authentication** with secure storage
- **API Versioning** for backward compatibility
- **Environment Configuration** for different deployments

This guide provides a comprehensive foundation for transitioning from Next.js to Laravel + Nuxt.js. The patterns and examples are based on the actual project structure, making them immediately applicable to your development workflow.
