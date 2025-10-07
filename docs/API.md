# API Documentation

## Overview

The Survey MVP API is a RESTful web service built with Laravel 12 that provides endpoints for managing surveys, questions, and user authentication. The API follows REST conventions and returns JSON responses.

## Base Information

- **Base URL**: `http://127.0.0.1:8000/api/v1`
- **Protocol**: HTTP/HTTPS
- **Data Format**: JSON
- **Authentication**: Bearer Token (Laravel Sanctum)

## Authentication

### Overview

The API uses Laravel Sanctum for token-based authentication. All protected endpoints require a valid Bearer token in the Authorization header.

### Authentication Flow

1. **Register** or **Login** to receive a token
2. Include the token in the `Authorization` header for protected requests
3. Token expires after 24 hours (configurable)

### Headers

```http
Authorization: Bearer {your-token-here}
Content-Type: application/json
Accept: application/json
```

## Endpoints

### Authentication Endpoints

#### Register User

**POST** `/auth/register`

Creates a new user account and returns an authentication token.

**Request Body:**

```json
{
  "name": "string (required, max:255)",
  "email": "string (required, email, unique)",
  "password": "string (required, min:8)",
  "password_confirmation": "string (required, same:password)"
}
```

**Response (201 Created):**

```json
{
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2025-01-07T10:00:00.000000Z",
      "updated_at": "2025-01-07T10:00:00.000000Z"
    },
    "token": "1|abc123def456..."
  },
  "message": "User registered successfully"
}
```

**Error Responses:**

- `422 Unprocessable Entity` - Validation errors
- `500 Internal Server Error` - Server error

#### Login User

**POST** `/auth/login`

Authenticates a user and returns an authentication token.

**Request Body:**

```json
{
  "email": "string (required, email)",
  "password": "string (required)"
}
```

**Response (200 OK):**

```json
{
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2025-01-07T10:00:00.000000Z",
      "updated_at": "2025-01-07T10:00:00.000000Z"
    },
    "token": "1|abc123def456..."
  },
  "message": "Login successful"
}
```

**Error Responses:**

- `401 Unauthorized` - Invalid credentials
- `422 Unprocessable Entity` - Validation errors

#### Logout User

**POST** `/auth/logout`

Revokes the current authentication token.

**Headers:**

```http
Authorization: Bearer {token}
```

**Response (200 OK):**

```json
{
  "message": "Logged out successfully"
}
```

**Error Responses:**

- `401 Unauthorized` - Invalid or missing token

### Survey Endpoints

#### List Surveys

**GET** `/surveys`

Retrieves a paginated list of surveys for the authenticated user.

**Headers:**

```http
Authorization: Bearer {token}
```

**Query Parameters:**

- `page` (integer, optional) - Page number for pagination
- `per_page` (integer, optional) - Number of items per page (max: 100)
- `filter[title]` (string, optional) - Filter by survey title
- `filter[status]` (string, optional) - Filter by survey status
- `include` (string, optional) - Include related resources (e.g., "questions")

**Response (200 OK):**

```json
{
  "data": [
    {
      "id": 1,
      "title": "Customer Satisfaction Survey",
      "description": "Help us improve our services",
      "status": "draft",
      "user_id": 1,
      "question_count": 5,
      "response_count": 0,
      "created_at": "2025-01-07T10:00:00.000000Z",
      "updated_at": "2025-01-07T10:00:00.000000Z",
      "questions": [
        {
          "id": 1,
          "title": "What is your name?",
          "type": "text_short",
          "is_required": true,
          "order": 0
        }
      ]
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/v1/surveys?page=1",
    "last": "http://127.0.0.1:8000/api/v1/surveys?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "per_page": 15,
    "to": 1,
    "total": 1
  }
}
```

#### Create Survey

**POST** `/surveys`

Creates a new survey for the authenticated user.

**Headers:**

```http
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**

```json
{
  "title": "string (required, max:255)",
  "description": "string (optional, max:1000)",
  "status": "string (optional, enum:draft|published|closed, default:draft)"
}
```

**Response (201 Created):**

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

#### Get Survey

**GET** `/surveys/{id}`

Retrieves a specific survey by ID.

**Headers:**

```http
Authorization: Bearer {token}
```

**Path Parameters:**

- `id` (integer, required) - Survey ID

**Response (200 OK):**

```json
{
  "data": {
    "id": 1,
    "title": "Customer Satisfaction Survey",
    "description": "Help us improve our services",
    "status": "draft",
    "user_id": 1,
    "question_count": 2,
    "response_count": 0,
    "created_at": "2025-01-07T10:00:00.000000Z",
    "updated_at": "2025-01-07T10:00:00.000000Z"
  },
  "message": "Survey retrieved successfully"
}
```

**Error Responses:**

- `404 Not Found` - Survey not found
- `403 Forbidden` - User doesn't own the survey

#### Update Survey

**PUT** `/surveys/{id}`

Updates an existing survey.

**Headers:**

```http
Authorization: Bearer {token}
Content-Type: application/json
```

**Path Parameters:**

- `id` (integer, required) - Survey ID

**Request Body:**

```json
{
  "title": "string (optional, max:255)",
  "description": "string (optional, max:1000)",
  "status": "string (optional, enum:draft|published|closed)"
}
```

**Response (200 OK):**

```json
{
  "data": {
    "id": 1,
    "title": "Updated Survey Title",
    "description": "Updated description",
    "status": "published",
    "user_id": 1,
    "question_count": 2,
    "response_count": 0,
    "created_at": "2025-01-07T10:00:00.000000Z",
    "updated_at": "2025-01-07T10:30:00.000000Z"
  },
  "message": "Survey updated successfully"
}
```

#### Delete Survey

**DELETE** `/surveys/{id}`

Deletes a survey and all its questions.

**Headers:**

```http
Authorization: Bearer {token}
```

**Path Parameters:**

- `id` (integer, required) - Survey ID

**Response (200 OK):**

```json
{
  "message": "Survey deleted successfully"
}
```

### Question Endpoints

#### List Survey Questions

**GET** `/surveys/{survey}/questions`

Retrieves all questions for a specific survey.

**Headers:**

```http
Authorization: Bearer {token}
```

**Path Parameters:**

- `survey` (integer, required) - Survey ID

**Response (200 OK):**

```json
{
  "data": [
    {
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
    {
      "id": 2,
      "survey_id": 1,
      "title": "What is your favorite color?",
      "description": "Please select one option",
      "type": "multiple_choice_single",
      "type_display": "Multiple Choice",
      "options": ["Red", "Blue", "Green"],
      "validation_rules": null,
      "is_required": true,
      "order": 1,
      "created_at": "2025-01-07T10:05:00.000000Z",
      "updated_at": "2025-01-07T10:05:00.000000Z"
    }
  ]
}
```

#### Create Question

**POST** `/surveys/{survey}/questions`

Creates a new question for a survey.

**Headers:**

```http
Authorization: Bearer {token}
Content-Type: application/json
```

**Path Parameters:**

- `survey` (integer, required) - Survey ID

**Request Body:**

```json
{
  "title": "string (required, max:255)",
  "description": "string (optional, max:1000)",
  "type": "string (required, enum:text_short|text_long|multiple_choice_single|multiple_choice_multiple|rating_scale|yes_no|dropdown|date|time|datetime|file_upload)",
  "options": "array (optional, required for multiple choice types)",
  "validation_rules": "object (optional)",
  "is_required": "boolean (optional, default:false)",
  "order": "integer (optional, default:0)"
}
```

**Example for Multiple Choice:**

```json
{
  "title": "What is your favorite color?",
  "description": "Please select one option",
  "type": "multiple_choice_single",
  "options": ["Red", "Blue", "Green", "Yellow"],
  "is_required": true,
  "order": 0
}
```

**Response (201 Created):**

```json
{
  "data": {
    "id": 1,
    "survey_id": 1,
    "title": "What is your favorite color?",
    "description": "Please select one option",
    "type": "multiple_choice_single",
    "type_display": "Multiple Choice",
    "options": ["Red", "Blue", "Green", "Yellow"],
    "validation_rules": null,
    "is_required": true,
    "order": 0,
    "created_at": "2025-01-07T10:00:00.000000Z",
    "updated_at": "2025-01-07T10:00:00.000000Z"
  },
  "message": "Question created successfully"
}
```

#### Get Question

**GET** `/questions/{id}`

Retrieves a specific question by ID.

**Headers:**

```http
Authorization: Bearer {token}
```

**Path Parameters:**

- `id` (integer, required) - Question ID

**Response (200 OK):**

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
  "message": "Question retrieved successfully"
}
```

#### Update Question

**PUT** `/questions/{id}`

Updates an existing question.

**Headers:**

```http
Authorization: Bearer {token}
Content-Type: application/json
```

**Path Parameters:**

- `id` (integer, required) - Question ID

**Request Body:**

```json
{
  "title": "string (optional, max:255)",
  "description": "string (optional, max:1000)",
  "type": "string (optional, enum:text_short|text_long|multiple_choice_single|multiple_choice_multiple|rating_scale|yes_no|dropdown|date|time|datetime|file_upload)",
  "options": "array (optional)",
  "validation_rules": "object (optional)",
  "is_required": "boolean (optional)",
  "order": "integer (optional)"
}
```

**Response (200 OK):**

```json
{
  "data": {
    "id": 1,
    "survey_id": 1,
    "title": "Updated Question Title",
    "description": "Updated description",
    "type": "text_short",
    "type_display": "Short Text",
    "options": null,
    "validation_rules": null,
    "is_required": true,
    "order": 0,
    "created_at": "2025-01-07T10:00:00.000000Z",
    "updated_at": "2025-01-07T10:30:00.000000Z"
  },
  "message": "Question updated successfully"
}
```

#### Delete Question

**DELETE** `/questions/{id}`

Deletes a question.

**Headers:**

```http
Authorization: Bearer {token}
```

**Path Parameters:**

- `id` (integer, required) - Question ID

**Response (200 OK):**

```json
{
  "message": "Question deleted successfully"
}
```

#### Reorder Questions

**POST** `/surveys/{survey}/questions/reorder`

Updates the order of questions within a survey.

**Headers:**

```http
Authorization: Bearer {token}
Content-Type: application/json
```

**Path Parameters:**

- `survey` (integer, required) - Survey ID

**Request Body:**

```json
{
  "question_ids": [2, 1, 3]
}
```

**Response (200 OK):**

```json
{
  "message": "Questions reordered successfully"
}
```

## Question Types

### Supported Question Types

| Type                       | Display Name    | Description               | Options Required |
| -------------------------- | --------------- | ------------------------- | ---------------- |
| `text_short`               | Short Text      | Single line text input    | No               |
| `text_long`                | Long Text       | Multi-line text area      | No               |
| `multiple_choice_single`   | Multiple Choice | Select one option         | Yes              |
| `multiple_choice_multiple` | Checkboxes      | Select multiple options   | Yes              |
| `rating_scale`             | Rating Scale    | Rate from 1-5 or 1-10     | No               |
| `yes_no`                   | Yes/No          | Simple yes or no question | No               |
| `dropdown`                 | Dropdown        | Select from dropdown menu | Yes              |
| `date`                     | Date            | Date picker               | No               |
| `time`                     | Time            | Time picker               | No               |
| `datetime`                 | Date & Time     | Date and time picker      | No               |
| `file_upload`              | File Upload     | Upload files              | No               |

### Question Type Examples

#### Text Short

```json
{
  "title": "What is your name?",
  "type": "text_short",
  "is_required": true
}
```

#### Multiple Choice Single

```json
{
  "title": "What is your favorite color?",
  "type": "multiple_choice_single",
  "options": ["Red", "Blue", "Green", "Yellow"],
  "is_required": true
}
```

#### Rating Scale

```json
{
  "title": "How satisfied are you with our service?",
  "type": "rating_scale",
  "validation_rules": {
    "min": 1,
    "max": 5
  },
  "is_required": true
}
```

## Error Handling

### Error Response Format

All error responses follow this format:

```json
{
  "message": "Error description",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

### HTTP Status Codes

| Code | Description                              |
| ---- | ---------------------------------------- |
| 200  | OK - Request successful                  |
| 201  | Created - Resource created successfully  |
| 400  | Bad Request - Invalid request data       |
| 401  | Unauthorized - Authentication required   |
| 403  | Forbidden - Access denied                |
| 404  | Not Found - Resource not found           |
| 422  | Unprocessable Entity - Validation errors |
| 429  | Too Many Requests - Rate limit exceeded  |
| 500  | Internal Server Error - Server error     |

### Common Error Examples

#### Validation Error (422)

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

#### Unauthorized (401)

```json
{
  "message": "Unauthenticated."
}
```

#### Not Found (404)

```json
{
  "message": "Survey not found."
}
```

#### Forbidden (403)

```json
{
  "message": "This action is unauthorized."
}
```

## Rate Limiting

The API implements rate limiting to prevent abuse:

- **Authentication endpoints**: 60 requests per minute
- **General API endpoints**: 60 requests per minute per user

Rate limit headers are included in responses:

```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1640995200
```

## Pagination

List endpoints support pagination with the following query parameters:

- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 15, max: 100)

Pagination metadata is included in list responses:

```json
{
  "data": [...],
  "links": {
    "first": "http://api.example.com/surveys?page=1",
    "last": "http://api.example.com/surveys?page=10",
    "prev": null,
    "next": "http://api.example.com/surveys?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "per_page": 15,
    "to": 15,
    "total": 150
  }
}
```

## Filtering and Sorting

### Filtering

Use the `filter` query parameter to filter results:

```http
GET /api/v1/surveys?filter[title]=Customer&filter[status]=published
```

### Sorting

Use the `sort` query parameter to sort results:

```http
GET /api/v1/surveys?sort=-created_at
```

- Prefix with `-` for descending order
- Default sort is by creation date (newest first)

## SDKs and Examples

### cURL Examples

#### Register User

```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

#### Create Survey

```bash
curl -X POST http://127.0.0.1:8000/api/v1/surveys \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Customer Satisfaction Survey",
    "description": "Help us improve our services",
    "status": "draft"
  }'
```

#### Create Question

```bash
curl -X POST http://127.0.0.1:8000/api/v1/surveys/1/questions \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Content-Type: application/json" \
  -d '{
    "title": "What is your name?",
    "type": "text_short",
    "is_required": true,
    "order": 0
  }'
```

### JavaScript/TypeScript Example

```typescript
class SurveyAPI {
  private baseURL = "http://127.0.0.1:8000/api/v1";
  private token: string | null = null;

  async login(email: string, password: string) {
    const response = await fetch(`${this.baseURL}/auth/login`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password }),
    });

    const data = await response.json();
    this.token = data.data.token;
    return data;
  }

  async createSurvey(surveyData: any) {
    const response = await fetch(`${this.baseURL}/surveys`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${this.token}`,
      },
      body: JSON.stringify(surveyData),
    });

    return response.json();
  }
}
```

---

**Last Updated**: January 7, 2025  
**API Version**: 1.0.0
