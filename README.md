# Survey MVP - Laravel 12 + Nuxt 4

A modern full-stack survey application built with Laravel 12 (backend API) and Nuxt.js 4 (frontend), following industry best practices.

## 🚀 Quick Start

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- npm or yarn

### Start Development Servers

```bash
# Terminal 1: Backend (Laravel)
cd backend && php artisan serve
# Runs on http://localhost:8000

# Terminal 2: Frontend (Nuxt)
cd frontend && npm run dev
# Runs on http://localhost:3000
```

## 📁 Project Structure

```
survey-laravel-nuxt-mvp/
├── backend/                    # Laravel 12 REST API
│   ├── app/Http/
│   │   ├── Controllers/Api/V1/ # Versioned API controllers
│   │   ├── Requests/           # Form validation
│   │   └── Resources/          # API response formatting
│   ├── database/
│   │   └── migrations/         # Database schema
│   └── routes/api.php          # API routes
│
├── frontend/                   # Nuxt 4 Frontend
│   └── app/                    # Main app directory (Nuxt 4 convention)
│       ├── composables/        # Auto-imported composables
│       ├── stores/             # Pinia state management
│       ├── pages/              # File-based routing
│       └── components/         # Vue components
│
└── SETUP_GUIDE.md             # Comprehensive setup documentation
```

## ✨ Features

### ✅ Implemented

- **Authentication**: Register, login, logout with Laravel Sanctum
- **Survey Management**: Full CRUD operations
  - Create, read, update, delete surveys
  - Filter by status (draft, published, closed)
  - Search by title
- **Authorization**: Users can only manage their own surveys
- **Responsive UI**: Modern design with Tailwind CSS
- **Type Safety**: Full TypeScript support
- **State Management**: Pinia with persistence

### 🚧 Planned

- Survey questions and answer types
- Survey responses collection
- Analytics and reporting
- Email notifications
- Survey sharing and public links

## 🛠 Technology Stack

### Backend

- **Laravel 12.32.5** - PHP framework
- **Laravel Sanctum 4.2.0** - API authentication
- **SQLite** - Development database
- **Spatie Query Builder** - Advanced filtering and sorting
- **PHPUnit** - Testing framework

### Frontend

- **Nuxt.js 4.1.2** - Vue.js framework
- **Pinia** - State management
- **Tailwind CSS v4** - Styling
- **TypeScript** - Type safety
- **$fetch (Ofetch)** - HTTP client

## 📖 Documentation

For detailed setup instructions, architecture decisions, and best practices, see:

- [SETUP_GUIDE.md](./SETUP_GUIDE.md) - Complete setup and development guide

## 🏗 Architecture Highlights

### Nuxt 4 App Directory Convention

All application code lives in `frontend/app/`:

- ✅ **Auto-imports**: Composables and stores automatically available
- ✅ **File-based routing**: Pages in `app/pages/` become routes
- ✅ **Relative imports**: Clear and consistent import paths
- ✅ **Type safety**: Full TypeScript support with inference

### Laravel Best Practices

- ✅ **API Versioning**: `/api/v1/` prefix
- ✅ **Form Requests**: Centralized validation
- ✅ **API Resources**: Consistent response formatting
- ✅ **Policies**: Authorization logic separation
- ✅ **Token Authentication**: Stateless API auth

## 🔐 Security

- **CORS**: Properly configured for frontend-backend communication
- **Authentication**: Laravel Sanctum with bearer token
- **Authorization**: Policy-based access control
- **Validation**: All inputs validated via Form Requests
- **SQL Injection**: Protected by Eloquent ORM
- **XSS**: Protected by Vue's automatic escaping

## 🧪 Testing

```bash
# Backend tests
cd backend && php artisan test

# Frontend tests (when implemented)
cd frontend && npm run test
```

## 📝 API Routes

### Public Routes

- `POST /api/v1/register` - User registration
- `POST /api/v1/login` - User login

### Protected Routes (requires authentication)

- `POST /api/v1/logout` - User logout
- `GET /api/v1/user` - Get current user
- `GET /api/v1/surveys` - List user's surveys
- `POST /api/v1/surveys` - Create survey
- `GET /api/v1/surveys/{id}` - Get survey details
- `PUT /api/v1/surveys/{id}` - Update survey
- `DELETE /api/v1/surveys/{id}` - Delete survey

## 🤝 Contributing

1. Follow the folder structure conventions
2. Use relative imports within `app/` directory
3. Validate all inputs via Form Requests (backend)
4. Use TypeScript for type safety (frontend)
5. Test your changes before committing

## 📄 License

This project is open-source and available for educational purposes.

## 🙏 Acknowledgments

- Built following Laravel and Nuxt.js best practices
- UI inspired by shadcn/ui design system
- Uses modern full-stack development patterns

---

**Status**: ✅ MVP Complete  
**Last Updated**: October 6, 2025
