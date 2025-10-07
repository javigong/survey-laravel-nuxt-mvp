# Contributing Guide

Thank you for your interest in contributing to the Survey MVP project! This guide will help you get started with contributing to our codebase.

## Table of Contents

1. [Code of Conduct](#code-of-conduct)
2. [Getting Started](#getting-started)
3. [Development Workflow](#development-workflow)
4. [Code Standards](#code-standards)
5. [Testing](#testing)
6. [Documentation](#documentation)
7. [Pull Request Process](#pull-request-process)
8. [Issue Reporting](#issue-reporting)
9. [Release Process](#release-process)

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code.

### Our Pledge

We pledge to make participation in our project a harassment-free experience for everyone, regardless of age, body size, disability, ethnicity, gender identity and expression, level of experience, nationality, personal appearance, race, religion, or sexual identity and orientation.

### Our Standards

Examples of behavior that contributes to creating a positive environment include:

- Using welcoming and inclusive language
- Being respectful of differing viewpoints and experiences
- Gracefully accepting constructive criticism
- Focusing on what is best for the community
- Showing empathy towards other community members

Examples of unacceptable behavior include:

- The use of sexualized language or imagery
- Trolling, insulting/derogatory comments, and personal or political attacks
- Public or private harassment
- Publishing others' private information without explicit permission
- Other conduct which could reasonably be considered inappropriate in a professional setting

## Getting Started

### Prerequisites

Before you begin, ensure you have the following installed:

- **PHP**: 8.4 or higher
- **Composer**: 2.0 or higher
- **Node.js**: 18 or higher
- **npm**: 8 or higher (or yarn 1.22+)
- **Git**: Latest version
- **Database**: MySQL 8.0+, PostgreSQL 13+, or SQLite 3.35+

### Fork and Clone

1. **Fork the repository** on GitHub
2. **Clone your fork** locally:

   ```bash
   git clone https://github.com/your-username/survey-laravel-nuxt-mvp.git
   cd survey-laravel-nuxt-mvp
   ```

3. **Add upstream remote**:
   ```bash
   git remote add upstream https://github.com/original-owner/survey-laravel-nuxt-mvp.git
   ```

### Development Setup

#### Backend Setup

```bash
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env file
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=survey_mvp
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed

# Start development server
php artisan serve
```

#### Frontend Setup

```bash
cd frontend

# Install dependencies
npm install

# Start development server
npm run dev
```

### Verify Setup

1. **Backend**: Visit `http://127.0.0.1:8000` - should show Laravel welcome page
2. **Frontend**: Visit `http://localhost:3000` - should show Survey MVP application
3. **API**: Test API endpoint `http://127.0.0.1:8000/api/v1/surveys` - should return JSON

## Development Workflow

### Branch Naming

Use descriptive branch names that follow this pattern:

```
type/description
```

**Types:**

- `feature/` - New features
- `fix/` - Bug fixes
- `docs/` - Documentation updates
- `refactor/` - Code refactoring
- `test/` - Test improvements
- `chore/` - Maintenance tasks

**Examples:**

- `feature/question-builder-enhancements`
- `fix/authentication-redirect-issue`
- `docs/api-documentation-update`
- `refactor/survey-component-structure`

### Commit Messages

Follow the [Conventional Commits](https://www.conventionalcommits.org/) specification:

```
type(scope): description

[optional body]

[optional footer(s)]
```

**Types:**

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

**Examples:**

```
feat(question-builder): add drag-and-drop reordering
fix(auth): resolve token expiration issue
docs(api): update authentication endpoints
test(survey): add unit tests for survey creation
```

### Workflow Steps

1. **Create a new branch**:

   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes**:

   - Write code following our standards
   - Add tests for new functionality
   - Update documentation as needed

3. **Test your changes**:

   ```bash
   # Backend tests
   cd backend
   php artisan test

   # Frontend tests
   cd frontend
   npm run test
   ```

4. **Commit your changes**:

   ```bash
   git add .
   git commit -m "feat(component): add new feature"
   ```

5. **Push to your fork**:

   ```bash
   git push origin feature/your-feature-name
   ```

6. **Create a Pull Request** on GitHub

## Code Standards

### PHP (Laravel Backend)

#### Coding Standards

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards
- Use Laravel Pint for code formatting:
  ```bash
  cd backend
  vendor/bin/pint
  ```

#### Code Style

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSurveyRequest;
use App\Http\Resources\SurveyResource;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $surveys = Survey::where('user_id', auth()->id())
            ->paginate(15);

        return response()->json([
            'data' => SurveyResource::collection($surveys),
            'message' => 'Surveys retrieved successfully'
        ]);
    }
}
```

#### Best Practices

- Use type hints for all parameters and return types
- Write PHPDoc blocks for all public methods
- Use Eloquent relationships instead of raw queries
- Implement proper error handling
- Use Form Requests for validation
- Follow Laravel naming conventions

### TypeScript (Vue Frontend)

#### Coding Standards

- Use TypeScript strict mode
- Follow Vue 3 Composition API patterns
- Use ESLint and Prettier for formatting

#### Code Style

```typescript
<script setup lang="ts">
interface Props {
  question: Question;
  isEditing?: boolean;
}

interface Emits {
  save: (question: Partial<Question>) => void;
  cancel: () => void;
}

const props = withDefaults(defineProps<Props>(), {
  isEditing: false
});

const emit = defineEmits<Emits>();

const handleSave = (questionData: Partial<Question>): void => {
  emit('save', questionData);
};
</script>
```

#### Best Practices

- Use Composition API with `<script setup>`
- Define interfaces for all data structures
- Use `ref` and `reactive` appropriately
- Implement proper error handling
- Use composables for reusable logic
- Follow Vue 3 style guide

### CSS (Tailwind)

#### Coding Standards

- Use utility-first approach
- Create custom components for repeated patterns
- Use dark mode variants consistently
- Follow mobile-first responsive design

#### Code Style

```vue
<template>
  <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
      {{ title }}
    </h3>
    <p class="text-sm text-gray-500 dark:text-gray-400">
      {{ description }}
    </p>
  </div>
</template>
```

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

#### Writing Tests

```php
<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use App\Models\Survey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurveyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_survey(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $surveyData = [
            'title' => 'Test Survey',
            'description' => 'Test Description',
            'status' => 'draft'
        ];

        $response = $this->postJson('/api/v1/surveys', $surveyData);

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

#### Writing Tests

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

## Documentation

### Code Documentation

#### PHP Documentation

```php
/**
 * Create a new survey for the authenticated user.
 *
 * @param StoreSurveyRequest $request The validated survey data
 * @return JsonResponse The created survey resource
 * @throws \Illuminate\Auth\Access\AuthorizationException
 */
public function store(StoreSurveyRequest $request): JsonResponse
{
    // Implementation
}
```

#### TypeScript Documentation

```typescript
/**
 * Composable for managing survey operations.
 *
 * @returns Object containing survey management functions
 */
export const useSurvey = () => {
  /**
   * Create a new survey.
   *
   * @param data - Survey creation data
   * @returns Promise resolving to created survey
   * @throws Error if creation fails
   */
  const createSurvey = async (data: CreateSurveyData): Promise<Survey> => {
    // Implementation
  };

  return { createSurvey };
};
```

### README Updates

When adding new features, update the relevant documentation:

1. **API Documentation** (`docs/API.md`)
2. **Frontend Documentation** (`docs/FRONTEND.md`)
3. **Deployment Guide** (`docs/DEPLOYMENT.md`)
4. **Changelog** (`docs/CHANGELOG.md`)

## Pull Request Process

### Before Submitting

1. **Ensure tests pass**:

   ```bash
   # Backend
   cd backend && php artisan test

   # Frontend
   cd frontend && npm run test
   ```

2. **Check code formatting**:

   ```bash
   # Backend
   cd backend && vendor/bin/pint

   # Frontend
   cd frontend && npm run lint
   ```

3. **Update documentation** if needed

4. **Rebase on latest main**:
   ```bash
   git checkout main
   git pull upstream main
   git checkout your-feature-branch
   git rebase main
   ```

### Pull Request Template

```markdown
## Description

Brief description of changes

## Type of Change

- [ ] Bug fix (non-breaking change which fixes an issue)
- [ ] New feature (non-breaking change which adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to not work as expected)
- [ ] Documentation update
- [ ] Refactoring (no functional changes)

## Testing

- [ ] Unit tests pass
- [ ] Integration tests pass
- [ ] Manual testing completed

## Checklist

- [ ] Code follows project style guidelines
- [ ] Self-review completed
- [ ] Documentation updated
- [ ] Tests added/updated
- [ ] No breaking changes (or breaking changes documented)

## Screenshots (if applicable)

Add screenshots to help explain your changes

## Related Issues

Closes #issue_number
```

### Review Process

1. **Automated Checks**: CI/CD pipeline runs tests and linting
2. **Code Review**: At least one maintainer reviews the code
3. **Testing**: Manual testing may be required
4. **Approval**: Maintainer approves and merges

## Issue Reporting

### Bug Reports

Use the bug report template:

```markdown
**Describe the bug**
A clear and concise description of what the bug is.

**To Reproduce**
Steps to reproduce the behavior:

1. Go to '...'
2. Click on '....'
3. Scroll down to '....'
4. See error

**Expected behavior**
A clear and concise description of what you expected to happen.

**Screenshots**
If applicable, add screenshots to help explain your problem.

**Environment:**

- OS: [e.g. Ubuntu 22.04]
- PHP Version: [e.g. 8.4]
- Node Version: [e.g. 18.17.0]
- Browser: [e.g. Chrome 120]

**Additional context**
Add any other context about the problem here.
```

### Feature Requests

Use the feature request template:

```markdown
**Is your feature request related to a problem? Please describe.**
A clear and concise description of what the problem is.

**Describe the solution you'd like**
A clear and concise description of what you want to happen.

**Describe alternatives you've considered**
A clear and concise description of any alternative solutions or features you've considered.

**Additional context**
Add any other context or screenshots about the feature request here.
```

## Release Process

### Version Numbering

We follow [Semantic Versioning](https://semver.org/):

- **MAJOR** (1.0.0): Breaking changes
- **MINOR** (0.1.0): New features, backwards compatible
- **PATCH** (0.0.1): Bug fixes, backwards compatible

### Release Steps

1. **Update version numbers** in:

   - `backend/composer.json`
   - `frontend/package.json`
   - `docs/CHANGELOG.md`

2. **Create release branch**:

   ```bash
   git checkout -b release/v1.0.0
   ```

3. **Update changelog** with new features and fixes

4. **Create pull request** for release

5. **Merge and tag**:

   ```bash
   git tag v1.0.0
   git push origin v1.0.0
   ```

6. **Deploy** to production

## Getting Help

### Communication Channels

- **GitHub Issues**: For bug reports and feature requests
- **GitHub Discussions**: For general questions and discussions
- **Pull Request Comments**: For code review discussions

### Resources

- **Laravel Documentation**: https://laravel.com/docs
- **Vue.js Documentation**: https://vuejs.org/guide/
- **Nuxt.js Documentation**: https://nuxt.com/docs
- **Tailwind CSS Documentation**: https://tailwindcss.com/docs

## Recognition

Contributors will be recognized in:

- **CONTRIBUTORS.md** file
- **Release notes** for significant contributions
- **GitHub contributors** page

Thank you for contributing to the Survey MVP project! 🎉

---

**Last Updated**: January 7, 2025  
**Contributing Guide Version**: 1.0.0
