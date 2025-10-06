# Complete Laravel + Nuxt.js Full-Stack Setup Guide

## Project Overview

This guide covers the complete setup of a modern full-stack survey application using Laravel 11 (latest stable) for the backend API and Nuxt.js 4 for the frontend, following industry best practices.

## Table of Contents

1. [Project Architecture](#project-architecture)
2. [Environment Requirements](#environment-requirements)
3. [Project Structure](#project-structure)
4. [Backend Setup (Laravel)](#backend-setup-laravel)
5. [Frontend Setup (Nuxt.js)](#frontend-setup-nuxtjs)
6. [Database Configuration](#database-configuration)
7. [Authentication System](#authentication-system)
8. [API Design & Best Practices](#api-design--best-practices)
9. [Frontend Architecture](#frontend-architecture)
10. [Security Implementation](#security-implementation)
11. [Testing Strategy](#testing-strategy)
12. [Deployment Guide](#deployment-guide)
13. [Development Workflow](#development-workflow)

## Project Architecture

### Monorepo Structure

We use a monorepo approach for better development workflow and simplified dependency management:

```
survey-laravel-nuxt-mvp/
├── backend/                    # Laravel 11 API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   ├── Requests/
│   │   │   ├── Resources/
│   │   │   └── Middleware/
│   │   ├── Models/
│   │   └── Services/
│   ├── config/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/
│   │   ├── api.php
│   │   └── web.php
│   ├── tests/
│   ├── .env.example
│   └── composer.json
├── frontend/                   # Nuxt.js 4 SPA
│   ├── components/
│   │   ├── UI/
│   │   ├── Forms/
│   │   └── Survey/
│   ├── composables/
│   ├── layouts/
│   ├── middleware/
│   ├── pages/
│   ├── plugins/
│   ├── stores/                 # Pinia stores
│   ├── types/                  # TypeScript definitions
│   ├── utils/
│   ├── .env.example
│   ├── nuxt.config.ts
│   └── package.json
├── docker-compose.yml          # Development environment
├── .gitignore
├── README.md
├── SETUP_GUIDE.md             # This file
└── scripts/
    ├── setup.sh
    ├── deploy-backend.sh
    └── deploy-frontend.sh
```

### Technology Stack

#### Backend (Laravel 11)

- **Framework**: Laravel 11.x (latest stable)
- **Authentication**: Laravel Sanctum (SPA + API tokens)
- **Database**: SQLite (dev) → PostgreSQL/MySQL (production)
- **API**: RESTful with JSON:API compliance
- **Validation**: Form Requests
- **Testing**: PHPUnit (Laravel's built-in testing framework)
- **Code Quality**: Laravel Pint, PHPStan

#### Frontend (Nuxt.js 4)

- **Framework**: Nuxt.js 4.x
- **Rendering**: SSR/SPA hybrid with improved performance
- **State Management**: Pinia with persistence
- **Styling**: Tailwind CSS v4
- **HTTP Client**: Built-in $fetch (Ofetch)
- **Testing**: Vitest + Vue Test Utils
- **TypeScript**: Full TypeScript support with better inference

## Implementation Status

### ✅ Completed Components

#### Backend (Laravel 12)
- **✅ Framework**: Laravel 12.32.5 installed
- **✅ Authentication**: Laravel Sanctum 4.2.0 configured
- **✅ Database**: SQLite database created with migrations
- **✅ API Structure**: Controllers and resources created
- **✅ Models**: User and Survey models with relationships
- **✅ Validation**: Form Requests for API validation
- **✅ Testing**: PHPUnit configured (Laravel's built-in testing)
- **✅ Code Quality**: Laravel Pint included

#### Created Files
```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/V1/
│   │   │   │   ├── AuthController.php       # Authentication endpoints
│   │   │   │   └── SurveyController.php     # Survey CRUD endpoints
│   │   └── Requests/
│   │       ├── LoginRequest.php            # Login validation
│   │       └── StoreSurveyRequest.php      # Survey creation validation
│   ├── Models/
│   │   ├── User.php                        # User model (Sanctum enabled)
│   │   └── Survey.php                      # Survey model
│   └── Resources/
│       └── SurveyResource.php              # API response formatting
├── config/
│   └── sanctum.php                         # Sanctum configuration
├── database/
│   ├── database.sqlite                     # SQLite database
│   └── migrations/
│       ├── 2025_10_06_041250_create_surveys_table.php
│       └── 2025_10_06_040235_create_personal_access_tokens_table.php
└── routes/
    └── api.php                             # API routes (to be configured)
```

#### Database Schema
- **users table**: Standard Laravel users with Sanctum support
- **personal_access_tokens table**: Sanctum token storage
- **surveys table**: Survey data storage

### 🔄 In Progress
- API route configuration
- Controller implementation
- Frontend Nuxt.js setup

### 📋 Next Steps
1. Configure API routes with proper middleware
2. Implement authentication endpoints
3. Implement survey CRUD operations
4. Set up Nuxt.js 4 frontend
5. Configure API integration
6. Add comprehensive testing

## Environment Requirements

### Required Software

- **PHP**: 8.3+ (required for Laravel 12)
- **Composer**: 2.8+
- **Node.js**: 20+ LTS (required for Nuxt 4)
- **npm/pnpm**: Latest (pnpm preferred)
- **Git**: Latest
- **Docker**: Optional but recommended

### Development Tools (Optional)

- **VS Code** with extensions:
  - PHP Intelephense
  - Laravel Extension Pack
  - Vetur/Volar for Vue
  - Tailwind CSS IntelliSense
- **TablePlus/phpMyAdmin** for database management
- **Postman/Insomnia** for API testing

## Backend Setup (Laravel)

### Installation Steps

1. **Create Laravel Project**

```bash
composer create-project laravel/laravel backend
cd backend
```

2. **Install Required Packages**

```bash
# Authentication
composer require laravel/sanctum

# API utilities
composer require spatie/laravel-query-builder

# Development tools
composer require --dev larastan/larastan
```

3. **Environment Configuration**

```bash
cp .env.example .env
php artisan key:generate
```

### Laravel Configuration

#### Database Configuration (.env)

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=survey_app
# DB_USERNAME=root
# DB_PASSWORD=
```

#### Sanctum Configuration

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

#### API Configuration (config/sanctum.php)

```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
    Sanctum::currentApplicationUrlWithPort()
))),
```

#### CORS Configuration (config/cors.php)

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => ['http://localhost:3000'],
'allowed_origins_patterns' => [],
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => true,
```

### API Structure

#### Route Organization (routes/api.php)

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/register', [V1\AuthController::class, 'register']);
    Route::post('/login', [V1\AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [V1\AuthController::class, 'logout']);
        Route::get('/user', [V1\AuthController::class, 'user']);

        // Survey resources
        Route::apiResource('surveys', V1\SurveyController::class);
        Route::apiResource('surveys.questions', V1\QuestionController::class);
        Route::apiResource('surveys.responses', V1\ResponseController::class);
    });
});
```

#### Controller Structure

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSurveyRequest;
use App\Http\Resources\SurveyResource;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;

class SurveyController extends Controller
{
    public function index(): JsonResponse
    {
        $surveys = Survey::query()
            ->allowedFilters(['title', 'status'])
            ->allowedSorts(['created_at', 'title'])
            ->paginate();

        return SurveyResource::collection($surveys)->response();
    }

    public function store(StoreSurveyRequest $request): JsonResponse
    {
        $survey = Survey::create($request->validated());

        return SurveyResource::make($survey)
            ->response()
            ->setStatusCode(201);
    }
}
```

#### Form Requests

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => 'in:draft,published,closed',
            'questions' => 'array',
            'questions.*.text' => 'required|string|max:500',
            'questions.*.type' => 'required|in:text,multiple_choice,rating',
        ];
    }
}
```

#### API Resources

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SurveyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'questions_count' => $this->whenCounted('questions'),
            'responses_count' => $this->whenCounted('responses'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'questions' => QuestionResource::collection($this->whenLoaded('questions')),
        ];
    }
}
```

## Frontend Setup (Nuxt.js)

### Installation Steps

1. **Create Nuxt Project**

```bash
npx nuxi@latest init frontend --nuxt-version=4
cd frontend
```

2. **Install Dependencies**

```bash
# Core dependencies
npm install @pinia/nuxt @pinia-plugin-persistedstate

# UI and styling (Tailwind CSS v4)
npm install @nuxtjs/tailwindcss@next @nuxtjs/color-mode
npm install @headlessui/vue @heroicons/vue

# Utilities
npm install @vueuse/nuxt zod

# Development dependencies
npm install --save-dev vitest @vue/test-utils
```

### Nuxt Configuration

#### nuxt.config.ts

```typescript
export default defineNuxtConfig({
  devtools: { enabled: true },

  // Rendering strategy
  ssr: true,

  // CSS framework
  css: ["~/assets/css/main.css"],

  // Modules
  modules: [
    "@pinia/nuxt",
    "@nuxtjs/tailwindcss",
    "@nuxtjs/color-mode",
    "@vueuse/nuxt",
  ],

  // Runtime configuration
  runtimeConfig: {
    public: {
      apiBase: process.env.API_BASE_URL || "http://localhost:8000/api/v1",
      appName: process.env.APP_NAME || "Survey App",
    },
  },

  // Color mode configuration
  colorMode: {
    classSuffix: "",
  },

  // Tailwind configuration
  tailwindcss: {
    cssPath: "~/assets/css/tailwind.css",
  },

  // Build configuration
  build: {
    transpile: ["@headlessui/vue"],
  },
});
```

#### Environment Configuration (.env)

```env
API_BASE_URL=http://localhost:8000/api/v1
APP_NAME=Survey App
NUXT_SECRET_KEY=your-secret-key-here
```

### Frontend Architecture

#### Store Structure (Pinia)

```typescript
// stores/auth.ts
import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null as User | null,
    token: null as string | null,
    isAuthenticated: false,
  }),

  actions: {
    async login(credentials: LoginCredentials) {
      try {
        const { data } = await $fetch("/auth/login", {
          method: "POST",
          body: credentials,
        });

        this.setAuth(data.user, data.token);
        await navigateTo("/dashboard");
      } catch (error) {
        throw error;
      }
    },

    async logout() {
      try {
        await $fetch("/auth/logout", {
          method: "POST",
          headers: {
            Authorization: `Bearer ${this.token}`,
          },
        });
      } finally {
        this.clearAuth();
        await navigateTo("/login");
      }
    },

    setAuth(user: User, token: string) {
      this.user = user;
      this.token = token;
      this.isAuthenticated = true;
    },

    clearAuth() {
      this.user = null;
      this.token = null;
      this.isAuthenticated = false;
    },
  },

  persist: {
    storage: persistedState.localStorage,
  },
});
```

#### API Composable

```typescript
// composables/useApi.ts
export const useApi = () => {
  const config = useRuntimeConfig();
  const authStore = useAuthStore();

  const api = $fetch.create({
    baseURL: config.public.apiBase,
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
        authStore.clearAuth();
        navigateTo("/login");
      }
    },
  });

  return { api };
};
```

#### Type Definitions

```typescript
// types/index.ts
export interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at?: string;
  created_at: string;
  updated_at: string;
}

export interface Survey {
  id: number;
  title: string;
  description?: string;
  status: "draft" | "published" | "closed";
  questions_count?: number;
  responses_count?: number;
  created_at: string;
  updated_at: string;
  questions?: Question[];
}

export interface Question {
  id: number;
  survey_id: number;
  text: string;
  type: "text" | "multiple_choice" | "rating";
  options?: string[];
  required: boolean;
  order: number;
}

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterData {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}
```

## Database Configuration

### Development (SQLite)

```bash
# Create SQLite database
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

### Production Migration

```php
// For PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=your-host
DB_PORT=5432
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password

// For MySQL
DB_CONNECTION=mysql
DB_HOST=your-host
DB_PORT=3306
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

### Database Migrations

```php
<?php
// database/migrations/create_surveys_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
```

## Authentication System

### Laravel Sanctum Setup

```php
// app/Http/Controllers/Api/V1/AuthController.php

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
            ]
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
            ]
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
}
```

### Frontend Authentication

```vue
<!-- pages/login.vue -->
<template>
  <div class="min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Sign in to your account
        </h2>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
        <div>
          <label for="email" class="sr-only">Email address</label>
          <input
            id="email"
            v-model="form.email"
            name="email"
            type="email"
            required
            class="relative block w-full px-3 py-2 border border-gray-300 rounded-md"
            placeholder="Email address"
          />
        </div>
        <div>
          <label for="password" class="sr-only">Password</label>
          <input
            id="password"
            v-model="form.password"
            name="password"
            type="password"
            required
            class="relative block w-full px-3 py-2 border border-gray-300 rounded-md"
            placeholder="Password"
          />
        </div>
        <div>
          <button
            type="submit"
            :disabled="loading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            {{ loading ? "Signing in..." : "Sign in" }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
const authStore = useAuthStore();
const router = useRouter();

const form = reactive({
  email: "",
  password: "",
});

const loading = ref(false);

const handleLogin = async () => {
  loading.value = true;
  try {
    await authStore.login(form);
  } catch (error) {
    console.error("Login failed:", error);
  } finally {
    loading.value = false;
  }
};
</script>
```

## API Design & Best Practices

### RESTful API Structure

```
GET    /api/v1/surveys              # List all surveys
POST   /api/v1/surveys              # Create a new survey
GET    /api/v1/surveys/{id}         # Get specific survey
PUT    /api/v1/surveys/{id}         # Update survey
DELETE /api/v1/surveys/{id}         # Delete survey

GET    /api/v1/surveys/{id}/questions    # List survey questions
POST   /api/v1/surveys/{id}/questions    # Add question to survey
PUT    /api/v1/questions/{id}            # Update specific question
DELETE /api/v1/questions/{id}            # Delete question

POST   /api/v1/surveys/{id}/responses    # Submit survey response
GET    /api/v1/surveys/{id}/responses    # Get survey responses (admin)
```

### Response Format

```json
{
  "data": {
    "id": 1,
    "title": "Customer Satisfaction Survey",
    "description": "Help us improve our service",
    "status": "published",
    "questions_count": 5,
    "responses_count": 23,
    "created_at": "2024-01-15T10:30:00Z",
    "updated_at": "2024-01-15T10:30:00Z"
  },
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 45
  },
  "links": {
    "first": "/api/v1/surveys?page=1",
    "last": "/api/v1/surveys?page=3",
    "prev": null,
    "next": "/api/v1/surveys?page=2"
  }
}
```

### Error Handling

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."],
    "email": ["The email has already been taken."]
  }
}
```

## Security Implementation

### Backend Security

1. **CSRF Protection**: Enabled by default for web routes
2. **Rate Limiting**: Applied to API routes
3. **Input Validation**: Form Requests for all inputs
4. **SQL Injection Prevention**: Eloquent ORM
5. **XSS Protection**: Output escaping

### Frontend Security

1. **XSS Prevention**: Vue.js automatic escaping
2. **CSRF Token**: Handled by Sanctum
3. **Secure Storage**: HttpOnly cookies for tokens
4. **Input Sanitization**: Zod validation

### Rate Limiting Configuration

```php
// app/Http/Kernel.php
protected $middlewareGroups = [
    'api' => [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        'throttle:api',
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
];

// routes/api.php
Route::middleware(['throttle:60,1'])->group(function () {
    // API routes
});
```

## Testing Strategy

### Backend Testing (PHPUnit)

```php
<?php

// tests/Feature/SurveyTest.php

namespace Tests\Feature;

use App\Models\Survey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurveyTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_survey()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/surveys', [
                'title' => 'Test Survey',
                'description' => 'A test survey'
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('surveys', [
            'title' => 'Test Survey',
            'user_id' => $user->id
        ]);
    }

    public function test_requires_authentication_to_create_survey()
    {
        $response = $this->postJson('/api/v1/surveys', [
            'title' => 'Test Survey'
        ]);

        $response->assertStatus(401);
    }
}
```

### Frontend Testing (Vitest)

```typescript
// tests/components/SurveyCard.test.ts
import { describe, it, expect } from "vitest";
import { mount } from "@vue/test-utils";
import SurveyCard from "~/components/SurveyCard.vue";

describe("SurveyCard", () => {
  it("renders survey information correctly", () => {
    const survey = {
      id: 1,
      title: "Test Survey",
      description: "A test survey",
      status: "published",
      questions_count: 5,
      responses_count: 10,
    };

    const wrapper = mount(SurveyCard, {
      props: { survey },
    });

    expect(wrapper.text()).toContain("Test Survey");
    expect(wrapper.text()).toContain("A test survey");
    expect(wrapper.text()).toContain("5 questions");
    expect(wrapper.text()).toContain("10 responses");
  });
});
```

## Deployment Guide

### Backend Deployment (Railway/Heroku)

#### Railway Setup

```bash
# Install Railway CLI
npm install -g @railway/cli

# Login and deploy
railway login
railway init
railway add --database postgresql
railway deploy
```

#### Environment Variables

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.railway.app

DB_CONNECTION=pgsql
DATABASE_URL=postgresql://user:pass@host:port/db

SANCTUM_STATEFUL_DOMAINS=your-frontend-domain.vercel.app
```

### Frontend Deployment (Vercel)

#### Vercel Setup

```bash
# Install Vercel CLI
npm install -g vercel

# Deploy
vercel --prod
```

#### Environment Variables

```env
API_BASE_URL=https://your-backend.railway.app/api/v1
APP_NAME=Survey App
```

### Docker Development Environment

```yaml
# docker-compose.yml
version: "3.8"

services:
  backend:
    build:
      context: ./backend
      dockerfile: Dockerfile
    ports:
      - "8000:8000"
    environment:
      - DB_CONNECTION=mysql
      - DB_HOST=mysql
      - DB_DATABASE=survey_app
      - DB_USERNAME=root
      - DB_PASSWORD=secret
    depends_on:
      - mysql
    volumes:
      - ./backend:/var/www/html

  frontend:
    build:
      context: ./frontend
      dockerfile: Dockerfile
    ports:
      - "3000:3000"
    environment:
      - API_BASE_URL=http://backend:8000/api/v1
    volumes:
      - ./frontend:/app
      - /app/node_modules

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: secret
      MYSQL_DATABASE: survey_app
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql

volumes:
  mysql_data:
```

## Development Workflow

### Daily Development

```bash
# Start backend
cd backend
php artisan serve

# Start frontend (new terminal)
cd frontend
npm run dev

# Run tests
cd backend && php artisan test
cd frontend && npm run test
```

### Git Workflow

```bash
# Feature development
git checkout -b feature/survey-creation
# ... make changes
git add .
git commit -m "feat: add survey creation functionality"
git push origin feature/survey-creation
# Create pull request

# Code review and merge
git checkout main
git pull origin main
git branch -d feature/survey-creation
```

### Code Quality

```bash
# Backend
php artisan pint       # Code formatting (Laravel Pint)
php artisan test       # Run PHPUnit tests

# Frontend
npm run lint          # ESLint
npm run type-check    # TypeScript check
npm run test          # Run tests
```

## Troubleshooting

### Common Issues

1. **CORS Issues**

   - Check `config/cors.php` settings
   - Verify frontend URL in `SANCTUM_STATEFUL_DOMAINS`

2. **Authentication Issues**

   - Clear browser cookies
   - Check token storage in frontend
   - Verify Sanctum middleware setup

3. **Database Issues**

   - Run `php artisan migrate:fresh --seed`
   - Check database connection settings
   - Verify file permissions for SQLite

4. **Build Issues**
   - Clear caches: `php artisan cache:clear`, `npm run build`
   - Check Node.js version compatibility
   - Verify environment variables

### Performance Optimization

1. **Backend**

   - Enable OPcache in production
   - Use database indexing
   - Implement API caching
   - Optimize database queries

2. **Frontend**
   - Enable SSR for SEO
   - Implement lazy loading
   - Optimize images
   - Use CDN for assets

## Conclusion

This setup provides a robust, scalable foundation for a survey application with modern development practices, security considerations, and deployment strategies. The monorepo structure facilitates development while maintaining clear separation of concerns between frontend and backend.

Remember to:

- Keep dependencies updated
- Follow security best practices
- Write comprehensive tests
- Document API changes
- Monitor application performance

## Current Implementation Notes

### Libraries and Versions Used
- **Laravel**: 12.32.5 (latest stable)
- **Laravel Sanctum**: 4.2.0 (for API authentication)
- **Spatie Laravel Query Builder**: 6.3.5 (for advanced API filtering)
- **PHPUnit**: 11.5.42 (Laravel's built-in testing framework)
- **Nuxt.js**: 4.x (latest stable, to be implemented)
- **Tailwind CSS**: v4 (next generation, to be implemented)

### Architecture Decisions
- **Monorepo Structure**: Single repository with backend/ and frontend/ directories
- **API Versioning**: v1 namespace for future-proofing
- **Testing**: PHPUnit for backend (instead of Pest due to compatibility issues)
- **Database**: SQLite for development, easy migration to PostgreSQL/MySQL
- **Authentication**: Token-based with Laravel Sanctum for SPA integration

### Security Features Implemented
- **CSRF Protection**: Enabled for web routes
- **Rate Limiting**: Applied to API routes (60 requests per minute)
- **Input Validation**: Form Requests for all API endpoints
- **SQL Injection Prevention**: Eloquent ORM
- **XSS Protection**: Output escaping in templates

### Performance Considerations
- **OPcache**: Enabled in production for PHP optimization
- **Database Indexing**: To be implemented for frequently queried fields
- **API Caching**: To be implemented using Laravel Cache
- **CDN Integration**: Planned for static assets

## Resources and Documentation

For questions or issues, refer to the official documentation:

- [Laravel 12 Documentation](https://laravel.com/docs/12.x)
- [Laravel Sanctum Documentation](https://laravel.com/docs/sanctum)
- [Spatie Query Builder](https://spatie.be/docs/laravel-query-builder)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Nuxt.js 4 Documentation](https://nuxt.com/docs)
- [Pinia Documentation](https://pinia.vuejs.org/)
- [Tailwind CSS v4](https://tailwindcss.com/docs)
