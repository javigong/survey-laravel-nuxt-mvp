# Survey MVP - Technical Documentation

## Table of Contents

1. [Project Overview](#project-overview)
2. [Architecture](#architecture)
3. [API Documentation](#api-documentation)
4. [Frontend Documentation](#frontend-documentation)
5. [Database Schema](#database-schema)
6. [Authentication & Authorization](#authentication--authorization)
7. [Deployment Guide](#deployment-guide)
8. [Development Guide](#development-guide)
9. [Testing](#testing)
10. [Contributing](#contributing)

## Project Overview

The Survey MVP is a full-stack web application built with Laravel 12 (backend) and Nuxt.js 4 (frontend) that enables users to create, manage, and distribute surveys with various question types.

### Key Features

- **Survey Management**: Create, read, update, and delete surveys
- **Question Builder**: Dynamic question creation with 11 different question types
- **User Authentication**: Secure user registration and login
- **Real-time Preview**: Live preview of survey questions
- **Responsive Design**: Mobile-first, responsive UI
- **API-First Architecture**: RESTful API with proper versioning

### Technology Stack

- **Backend**: Laravel 12, PHP 8.4, SQLite/MySQL
- **Frontend**: Nuxt.js 4, Vue 3, TypeScript, Tailwind CSS
- **Authentication**: Laravel Sanctum
- **Database**: Eloquent ORM with migrations
- **API**: RESTful API with JSON responses

## Architecture

### System Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend API   │    │   Database      │
│   (Nuxt.js 4)   │◄──►│   (Laravel 12)  │◄──►│   (SQLite)      │
│                 │    │                 │    │                 │
│ • Vue 3         │    │ • REST API      │    │ • Surveys       │
│ • TypeScript    │    │ • Sanctum Auth  │    │ • Questions     │
│ • Tailwind CSS  │    │ • Eloquent ORM  │    │ • Answers       │
│ • Pinia Store   │    │ • Form Requests │    │ • Users         │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### Frontend Architecture

```
frontend/
├── app/
│   ├── components/          # Reusable Vue components
│   │   ├── QuestionEditor.vue
│   │   └── QuestionPreview.vue
│   ├── composables/         # Vue composables
│   │   ├── useAuth.ts
│   │   └── useSurvey.ts
│   ├── layouts/             # Page layouts
│   │   └── dashboard.vue
│   ├── pages/               # File-based routing
│   │   ├── dashboard/
│   │   │   └── surveys/
│   │   │       ├── index.vue
│   │   │       ├── create.vue
│   │   │       └── [id]/
│   │   │           └── edit.vue
│   │   ├── login.vue
│   │   └── register.vue
│   ├── stores/              # Pinia state management
│   │   ├── auth.ts
│   │   └── survey.ts
│   └── middleware/          # Route middleware
│       └── auth.ts
├── assets/                  # Static assets
├── nuxt.config.ts          # Nuxt configuration
└── package.json
```

### Backend Architecture

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/V1/     # API versioning
│   │   │       ├── AuthController.php
│   │   │       ├── SurveyController.php
│   │   │       └── QuestionController.php
│   │   ├── Requests/       # Form validation
│   │   │   ├── LoginRequest.php
│   │   │   └── StoreQuestionRequest.php
│   │   └── Resources/      # API resources
│   │       ├── SurveyResource.php
│   │       └── QuestionResource.php
│   ├── Models/             # Eloquent models
│   │   ├── User.php
│   │   ├── Survey.php
│   │   ├── Question.php
│   │   └── Answer.php
│   └── Policies/           # Authorization policies
│       └── SurveyPolicy.php
├── database/
│   └── migrations/         # Database migrations
├── routes/
│   └── api.php            # API routes
└── config/
    └── sanctum.php        # Sanctum configuration
```

## API Documentation

### Base URL

```
http://127.0.0.1:8000/api/v1
```

### Authentication

All protected endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer {token}
```

### Endpoints

#### Authentication

| Method | Endpoint         | Description       | Auth Required |
| ------ | ---------------- | ----------------- | ------------- |
| POST   | `/auth/register` | Register new user | No            |
| POST   | `/auth/login`    | Login user        | No            |
| POST   | `/auth/logout`   | Logout user       | Yes           |

#### Surveys

| Method | Endpoint        | Description         | Auth Required |
| ------ | --------------- | ------------------- | ------------- |
| GET    | `/surveys`      | List user's surveys | Yes           |
| POST   | `/surveys`      | Create new survey   | Yes           |
| GET    | `/surveys/{id}` | Get survey details  | Yes           |
| PUT    | `/surveys/{id}` | Update survey       | Yes           |
| DELETE | `/surveys/{id}` | Delete survey       | Yes           |

#### Questions

| Method | Endpoint                              | Description           | Auth Required |
| ------ | ------------------------------------- | --------------------- | ------------- |
| GET    | `/surveys/{survey}/questions`         | List survey questions | Yes           |
| POST   | `/surveys/{survey}/questions`         | Create question       | Yes           |
| GET    | `/questions/{id}`                     | Get question details  | Yes           |
| PUT    | `/questions/{id}`                     | Update question       | Yes           |
| DELETE | `/questions/{id}`                     | Delete question       | Yes           |
| POST   | `/surveys/{survey}/questions/reorder` | Reorder questions     | Yes           |

### Request/Response Examples

#### Register User

```http
POST /api/v1/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response:**

```json
{
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "1|abc123..."
  },
  "message": "User registered successfully"
}
```

#### Create Survey

```http
POST /api/v1/surveys
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "title": "Customer Satisfaction Survey",
  "description": "Help us improve our services",
  "status": "draft"
}
```

**Response:**

```json
{
  "data": {
    "id": 1,
    "title": "Customer Satisfaction Survey",
    "description": "Help us improve our services",
    "status": "draft",
    "user_id": 1,
    "question_count": 0,
    "response_count": 0,
    "created_at": "2025-01-07T10:00:00.000000Z",
    "updated_at": "2025-01-07T10:00:00.000000Z"
  },
  "message": "Survey created successfully"
}
```

#### Create Question

```http
POST /api/v1/surveys/1/questions
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "title": "What is your name?",
  "description": "Please enter your full name",
  "type": "text_short",
  "is_required": true,
  "order": 0
}
```

**Response:**

```json
{
  "data": {
    "id": 1,
    "survey_id": 1,
    "title": "What is your name?",
    "description": "Please enter your full name",
    "type": "text_short",
    "type_display": "Short Text",
    "options": null,
    "validation_rules": null,
    "is_required": true,
    "order": 0,
    "created_at": "2025-01-07T10:00:00.000000Z",
    "updated_at": "2025-01-07T10:00:00.000000Z"
  },
  "message": "Question created successfully"
}
```

## Frontend Documentation

### Component Architecture

#### QuestionEditor Component

**Purpose**: Modal component for creating and editing survey questions

**Props**:

- `question: Question` - Question object to edit (optional for new questions)

**Events**:

- `@save` - Emitted when question is saved with question data
- `@cancel` - Emitted when editing is cancelled

**Features**:

- Dynamic form based on question type
- Real-time validation
- Options management for multiple choice questions
- Required field validation

#### QuestionPreview Component

**Purpose**: Read-only preview of survey questions

**Props**:

- `question: Question` - Question object to preview

**Features**:

- Renders appropriate input type based on question type
- Shows validation states
- Displays options for multiple choice questions

### State Management

#### Auth Store (Pinia)

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

**Actions**:

- `login(credentials)` - Authenticate user
- `logout()` - Clear authentication state
- `register(userData)` - Register new user
- `initializeAuth()` - Initialize auth from localStorage

#### Survey Store (Pinia)

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

### Composables

#### useAuth Composable

```typescript
export const useAuth = () => {
  const login = async (credentials: LoginCredentials) => Promise<void>;
  const logout = async () => Promise<void>;
  const register = async (userData: RegisterData) => Promise<void>;

  return { login, logout, register };
};
```

#### useSurvey Composable

```typescript
export const useSurvey = () => {
  const fetchSurveys = async (filters?: SurveyFilters) => Promise<Survey[]>;
  const createSurvey = async (data: CreateSurveyData) => Promise<Survey>;
  const getSurvey = async (id: number) => Promise<Survey>;
  const updateSurvey = async (id: number, data: Partial<CreateSurveyData>) =>
    Promise<Survey>;
  const deleteSurvey = async (id: number) => Promise<void>;

  const fetchQuestions = async (surveyId: number) => Promise<Question[]>;
  const createQuestion = async (data: CreateQuestionData) => Promise<Question>;
  const updateQuestion = async (
    id: number,
    data: Partial<CreateQuestionData>
  ) => Promise<Question>;
  const deleteQuestion = async (id: number) => Promise<void>;
  const reorderQuestions = async (surveyId: number, questionIds: number[]) =>
    Promise<void>;

  return {
    fetchSurveys,
    createSurvey,
    getSurvey,
    updateSurvey,
    deleteSurvey,
    fetchQuestions,
    createQuestion,
    updateQuestion,
    deleteQuestion,
    reorderQuestions,
  };
};
```

## Database Schema

### Tables

#### users

| Column            | Type         | Constraints                 | Description                  |
| ----------------- | ------------ | --------------------------- | ---------------------------- |
| id                | bigint       | PRIMARY KEY, AUTO_INCREMENT | User ID                      |
| name              | varchar(255) | NOT NULL                    | User's full name             |
| email             | varchar(255) | UNIQUE, NOT NULL            | User's email address         |
| email_verified_at | timestamp    | NULLABLE                    | Email verification timestamp |
| password          | varchar(255) | NOT NULL                    | Hashed password              |
| created_at        | timestamp    | NOT NULL                    | Creation timestamp           |
| updated_at        | timestamp    | NOT NULL                    | Last update timestamp        |

#### surveys

| Column      | Type         | Constraints                 | Description           |
| ----------- | ------------ | --------------------------- | --------------------- |
| id          | bigint       | PRIMARY KEY, AUTO_INCREMENT | Survey ID             |
| user_id     | bigint       | FOREIGN KEY, NOT NULL       | Owner user ID         |
| title       | varchar(255) | NOT NULL                    | Survey title          |
| description | text         | NULLABLE                    | Survey description    |
| status      | enum         | NOT NULL, DEFAULT 'draft'   | Survey status         |
| created_at  | timestamp    | NOT NULL                    | Creation timestamp    |
| updated_at  | timestamp    | NOT NULL                    | Last update timestamp |

**Indexes**:

- `surveys_user_id_index` on `user_id`
- `surveys_status_index` on `status`

#### questions

| Column           | Type         | Constraints                 | Description                            |
| ---------------- | ------------ | --------------------------- | -------------------------------------- |
| id               | bigint       | PRIMARY KEY, AUTO_INCREMENT | Question ID                            |
| survey_id        | bigint       | FOREIGN KEY, NOT NULL       | Parent survey ID                       |
| title            | varchar(255) | NOT NULL                    | Question title                         |
| description      | text         | NULLABLE                    | Question description                   |
| type             | enum         | NOT NULL                    | Question type                          |
| options          | json         | NULLABLE                    | Question options (for multiple choice) |
| validation_rules | json         | NULLABLE                    | Validation rules                       |
| is_required      | boolean      | NOT NULL, DEFAULT false     | Required question flag                 |
| order            | integer      | NOT NULL, DEFAULT 0         | Question order                         |
| created_at       | timestamp    | NOT NULL                    | Creation timestamp                     |
| updated_at       | timestamp    | NOT NULL                    | Last update timestamp                  |

**Indexes**:

- `questions_survey_id_index` on `survey_id`
- `questions_order_index` on `order`
- `questions_type_index` on `type`

#### answers

| Column           | Type         | Constraints                 | Description                            |
| ---------------- | ------------ | --------------------------- | -------------------------------------- |
| id               | bigint       | PRIMARY KEY, AUTO_INCREMENT | Answer ID                              |
| question_id      | bigint       | FOREIGN KEY, NOT NULL       | Parent question ID                     |
| survey_id        | bigint       | FOREIGN KEY, NOT NULL       | Parent survey ID                       |
| respondent_id    | varchar(255) | NULLABLE                    | Respondent identifier                  |
| text_answer      | text         | NULLABLE                    | Text-based answer                      |
| selected_options | json         | NULLABLE                    | Selected options (for multiple choice) |
| rating_value     | integer      | NULLABLE                    | Rating value (for rating scale)        |
| boolean_answer   | boolean      | NULLABLE                    | Yes/No answer                          |
| date_answer      | date         | NULLABLE                    | Date answer                            |
| time_answer      | time         | NULLABLE                    | Time answer                            |
| datetime_answer  | datetime     | NULLABLE                    | DateTime answer                        |
| file_path        | varchar(255) | NULLABLE                    | File path (for file upload)            |
| file_name        | varchar(255) | NULLABLE                    | Original file name                     |
| created_at       | timestamp    | NOT NULL                    | Creation timestamp                     |
| updated_at       | timestamp    | NOT NULL                    | Last update timestamp                  |

**Indexes**:

- `answers_question_id_index` on `question_id`
- `answers_survey_id_index` on `survey_id`
- `answers_respondent_id_index` on `respondent_id`

### Relationships

```sql
-- Users to Surveys (One-to-Many)
users.id -> surveys.user_id

-- Surveys to Questions (One-to-Many)
surveys.id -> questions.survey_id

-- Questions to Answers (One-to-Many)
questions.id -> answers.question_id

-- Surveys to Answers (One-to-Many)
surveys.id -> answers.survey_id
```

## Authentication & Authorization

### Authentication Flow

1. **Registration**:

   - User provides name, email, password
   - Password is hashed using Laravel's Hash facade
   - User record is created
   - Sanctum token is generated
   - Token is returned to frontend

2. **Login**:

   - User provides email and password
   - Credentials are validated
   - Sanctum token is generated
   - Token is returned to frontend

3. **Token Usage**:
   - Frontend stores token in localStorage
   - Token is sent in Authorization header for protected routes
   - Backend validates token on each request

### Authorization

#### Survey Policy

```php
class SurveyPolicy
{
    public function view(User $user, Survey $survey): bool
    {
        return $user->id === $survey->user_id;
    }

    public function update(User $user, Survey $survey): bool
    {
        return $user->id === $survey->user_id;
    }

    public function delete(User $user, Survey $survey): bool
    {
        return $user->id === $survey->user_id;
    }
}
```

#### Question Authorization

Questions inherit authorization from their parent survey. Users can only manage questions for surveys they own.

### Security Features

- **Password Hashing**: Laravel's bcrypt hashing
- **CSRF Protection**: Disabled for API routes (using Sanctum tokens)
- **Rate Limiting**: API endpoints are rate limited
- **Input Validation**: All inputs are validated using Form Requests
- **SQL Injection Prevention**: Eloquent ORM provides protection
- **XSS Protection**: Output is escaped in Blade templates

## Deployment Guide

### Prerequisites

- PHP 8.4+
- Composer
- Node.js 18+
- npm or yarn
- Database (SQLite, MySQL, or PostgreSQL)

### Backend Deployment

1. **Install Dependencies**:

   ```bash
   cd backend
   composer install --optimize-autoloader --no-dev
   ```

2. **Environment Configuration**:

   ```bash
   cp .env.example .env
   # Edit .env with production values
   ```

3. **Database Setup**:

   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```

4. **Optimize for Production**:

   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. **Web Server Configuration**:
   - Point document root to `backend/public`
   - Configure URL rewriting for Laravel

### Frontend Deployment

1. **Build for Production**:

   ```bash
   cd frontend
   npm install
   npm run build
   ```

2. **Deploy Built Files**:
   - Upload `frontend/.output/public` to web server
   - Configure web server to serve static files

### Docker Deployment

```dockerfile
# Backend Dockerfile
FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    libzip-dev \
    zip \
    unzip

RUN docker-php-ext-install pdo pdo_mysql zip

COPY . .
RUN composer install --optimize-autoloader --no-dev

EXPOSE 9000
CMD ["php-fpm"]
```

```dockerfile
# Frontend Dockerfile
FROM node:18-alpine AS builder

WORKDIR /app
COPY package*.json ./
RUN npm ci --only=production

COPY . .
RUN npm run build

FROM nginx:alpine
COPY --from=builder /app/.output/public /usr/share/nginx/html
EXPOSE 80
```

## Development Guide

### Local Development Setup

1. **Clone Repository**:

   ```bash
   git clone <repository-url>
   cd survey-laravel-nuxt-mvp
   ```

2. **Backend Setup**:

   ```bash
   cd backend
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   php artisan serve
   ```

3. **Frontend Setup**:
   ```bash
   cd frontend
   npm install
   npm run dev
   ```

### Code Standards

#### PHP (Laravel)

- Follow PSR-12 coding standards
- Use Laravel Pint for code formatting
- Write PHPDoc blocks for all methods
- Use type hints for parameters and return types

#### TypeScript (Vue)

- Use TypeScript strict mode
- Define interfaces for all data structures
- Use composition API with `<script setup>`
- Follow Vue 3 style guide

#### CSS (Tailwind)

- Use utility-first approach
- Create custom components for repeated patterns
- Use dark mode variants
- Maintain consistent spacing scale

### Git Workflow

1. **Feature Branches**:

   ```bash
   git checkout -b feature/question-builder
   git commit -m "feat: implement question builder interface"
   git push origin feature/question-builder
   ```

2. **Commit Messages**:

   - Use conventional commits format
   - Examples: `feat:`, `fix:`, `docs:`, `test:`, `refactor:`

3. **Pull Requests**:
   - Include detailed description
   - Reference related issues
   - Request code review
   - Ensure tests pass

## Testing

### Backend Testing

#### Unit Tests

```bash
cd backend
php artisan test --testsuite=Unit
```

#### Feature Tests

```bash
cd backend
php artisan test --testsuite=Feature
```

#### Test Coverage

```bash
cd backend
php artisan test --coverage
```

### Frontend Testing

#### Unit Tests

```bash
cd frontend
npm run test:unit
```

#### E2E Tests

```bash
cd frontend
npm run test:e2e
```

### Test Examples

#### Backend Test Example

```php
class SurveyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_survey(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/v1/surveys', [
            'title' => 'Test Survey',
            'description' => 'Test Description',
            'status' => 'draft'
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => ['id', 'title', 'description', 'status']
                ]);

        $this->assertDatabaseHas('surveys', [
            'title' => 'Test Survey',
            'user_id' => $user->id
        ]);
    }
}
```

#### Frontend Test Example

```typescript
import { describe, it, expect } from "vitest";
import { mount } from "@vue/test-utils";
import QuestionEditor from "@/components/QuestionEditor.vue";

describe("QuestionEditor", () => {
  it("renders question form correctly", () => {
    const wrapper = mount(QuestionEditor, {
      props: {
        question: {
          id: 1,
          title: "Test Question",
          type: "text_short",
          is_required: true,
        },
      },
    });

    expect(wrapper.find('input[type="text"]').exists()).toBe(true);
    expect(wrapper.find('input[type="checkbox"]').element.checked).toBe(true);
  });
});
```

## Contributing

### Development Process

1. **Fork Repository**
2. **Create Feature Branch**
3. **Write Tests**
4. **Implement Feature**
5. **Update Documentation**
6. **Submit Pull Request**

### Code Review Checklist

- [ ] Code follows project standards
- [ ] Tests are written and passing
- [ ] Documentation is updated
- [ ] No security vulnerabilities
- [ ] Performance considerations addressed
- [ ] Error handling implemented

### Issue Reporting

When reporting issues, include:

- Clear description of the problem
- Steps to reproduce
- Expected vs actual behavior
- Environment details
- Screenshots if applicable

---

**Last Updated**: January 7, 2025  
**Version**: 1.0.0  
**Maintainer**: Development Team
