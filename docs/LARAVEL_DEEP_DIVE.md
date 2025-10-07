# Laravel Deep Dive for Next.js Developers

## Table of Contents

1. [Laravel Fundamentals](#laravel-fundamentals)
2. [Eloquent ORM Deep Dive](#eloquent-orm-deep-dive)
3. [Laravel Authentication & Security](#laravel-authentication--security)
4. [API Development Patterns](#api-development-patterns)
5. [Laravel Ecosystem](#laravel-ecosystem)
6. [Performance & Optimization](#performance--optimization)

## Laravel Fundamentals

### 1. Service Container & Dependency Injection

Laravel's service container is like React's Context API but for dependency management:

```php
// Service Provider (app/Providers/AppServiceProvider.php)
class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind interface to implementation
        $this->app->bind(SurveyRepositoryInterface::class, SurveyRepository::class);

        // Singleton binding
        $this->app->singleton(ApiClient::class, function ($app) {
            return new ApiClient(config('services.api.key'));
        });
    }
}

// Usage in Controller
class SurveyController extends Controller
{
    public function __construct(
        private SurveyRepositoryInterface $surveyRepository,
        private ApiClient $apiClient
    ) {}

    public function index()
    {
        return $this->surveyRepository->getUserSurveys(auth()->id());
    }
}
```

### 2. Artisan Commands (Laravel's CLI)

Think of Artisan as Next.js CLI but more powerful:

```bash
# Create a new model with migration and factory
php artisan make:model Survey -m -f

# Create a controller with resource methods
php artisan make:controller Api/V1/SurveyController --api

# Create a form request for validation
php artisan make:request StoreSurveyRequest

# Run database migrations
php artisan migrate

# Create a seeder
php artisan make:seeder SurveySeeder

# Run seeders
php artisan db:seed --class=SurveySeeder

# Clear all caches
php artisan optimize:clear

# Generate application key
php artisan key:generate
```

### 3. Configuration Management

Laravel's config system is more structured than Next.js environment variables:

```php
// config/survey.php
return [
    'max_questions_per_survey' => env('SURVEY_MAX_QUESTIONS', 50),
    'default_status' => 'draft',
    'allowed_question_types' => ['text', 'multiple_choice', 'rating'],
];

// Usage
$maxQuestions = config('survey.max_questions_per_survey');
$defaultStatus = config('survey.default_status');
```

## Eloquent ORM Deep Dive

### 1. Model Relationships

Eloquent relationships are more powerful than Prisma relations:

```php
// User Model
class User extends Model
{
    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    public function responses()
    {
        return $this->hasManyThrough(Response::class, Survey::class);
    }
}

// Survey Model
class Survey extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    // Polymorphic relationship
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}

// Question Model
class Question extends Model
{
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    // Accessor (computed property)
    public function getIsRequiredAttribute()
    {
        return $this->required === 1;
    }

    // Mutator (setter)
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = ucfirst(trim($value));
    }
}
```

### 2. Query Builder vs Eloquent

```php
// Query Builder (more control, similar to Prisma)
$surveys = DB::table('surveys')
    ->join('users', 'surveys.user_id', '=', 'users.id')
    ->where('surveys.status', 'published')
    ->where('users.email_verified_at', '!=', null)
    ->select('surveys.*', 'users.name as author_name')
    ->paginate(15);

// Eloquent (more readable, with relationships)
$surveys = Survey::with(['user', 'questions'])
    ->where('status', 'published')
    ->whereHas('user', function ($query) {
        $query->whereNotNull('email_verified_at');
    })
    ->paginate(15);

// Eager loading to prevent N+1 queries
$surveys = Survey::with(['user:id,name,email', 'questions:id,survey_id,title'])
    ->get();
```

### 3. Model Scopes and Global Scopes

```php
// Local Scopes
class Survey extends Model
{
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeWithQuestions($query)
    {
        return $query->with('questions');
    }
}

// Usage
$publishedSurveys = Survey::published()
    ->byUser(auth()->id())
    ->withQuestions()
    ->get();

// Global Scopes
class SoftDeletesScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereNull($model->getQualifiedDeletedAtColumn());
    }
}

// Apply to model
class Survey extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new SoftDeletesScope);
    }
}
```

### 4. Model Events and Observers

```php
// Model Events
class Survey extends Model
{
    protected static function booted()
    {
        static::creating(function ($survey) {
            $survey->slug = Str::slug($survey->title);
        });

        static::created(function ($survey) {
            // Send notification
            event(new SurveyCreated($survey));
        });

        static::updating(function ($survey) {
            if ($survey->isDirty('title')) {
                $survey->slug = Str::slug($survey->title);
            }
        });
    }
}

// Observer (for complex logic)
class SurveyObserver
{
    public function created(Survey $survey)
    {
        // Create default question
        $survey->questions()->create([
            'title' => 'Welcome to this survey',
            'type' => 'text',
            'required' => false,
            'order' => 1,
        ]);
    }

    public function deleting(Survey $survey)
    {
        // Soft delete related data
        $survey->questions()->delete();
        $survey->responses()->delete();
    }
}

// Register observer
class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Survey::observe(SurveyObserver::class);
    }
}
```

## Laravel Authentication & Security

### 1. Laravel Sanctum Deep Dive

```php
// Token Management
class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Create token with abilities
        $token = $user->createToken('auth-token', ['survey:read', 'survey:write']);

        return response()->json([
            'data' => [
                'user' => UserResource::make($user),
                'token' => $token->plainTextToken,
                'abilities' => $token->abilities,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function logoutAll(Request $request)
    {
        // Revoke all tokens
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'All sessions terminated']);
    }
}

// Middleware for token abilities
class CheckTokenAbility
{
    public function handle($request, Closure $next, $ability)
    {
        if (!$request->user()->tokenCan($ability)) {
            return response()->json(['message' => 'Insufficient permissions'], 403);
        }

        return $next($request);
    }
}

// Usage in routes
Route::middleware(['auth:sanctum', 'ability:survey:write'])->group(function () {
    Route::post('/surveys', [SurveyController::class, 'store']);
});
```

### 2. Authorization with Policies

```php
// Survey Policy
class SurveyPolicy
{
    public function view(User $user, Survey $survey)
    {
        return $survey->user_id === $user->id || $survey->status === 'published';
    }

    public function update(User $user, Survey $survey)
    {
        return $survey->user_id === $user->id;
    }

    public function delete(User $user, Survey $survey)
    {
        return $survey->user_id === $user->id;
    }

    public function viewResponses(User $user, Survey $survey)
    {
        return $survey->user_id === $user->id;
    }
}

// Controller usage
class SurveyController extends Controller
{
    public function show(Survey $survey)
    {
        $this->authorize('view', $survey);

        return new SurveyResource($survey);
    }

    public function update(UpdateSurveyRequest $request, Survey $survey)
    {
        $this->authorize('update', $survey);

        $survey->update($request->validated());

        return new SurveyResource($survey);
    }
}
```

### 3. Rate Limiting and Throttling

```php
// Custom rate limiting
class SurveyRateLimiter
{
    public function handle($request, Closure $next)
    {
        $key = 'survey_creation:' . $request->user()->id;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Too many survey creation attempts. Try again in {$seconds} seconds."
            ], 429);
        }

        RateLimiter::hit($key, 3600); // 1 hour window

        return $next($request);
    }
}

// Usage
Route::middleware(['auth:sanctum', SurveyRateLimiter::class])->group(function () {
    Route::post('/surveys', [SurveyController::class, 'store']);
});
```

## API Development Patterns

### 1. API Resources for Consistent Responses

```php
// Survey Resource
class SurveyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'slug' => $this->slug,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),

            // Conditional fields
            'questions_count' => $this->when($this->relationLoaded('questions'),
                fn() => $this->questions->count()),
            'responses_count' => $this->when($this->relationLoaded('responses'),
                fn() => $this->responses->count()),

            // Relationships
            'user' => new UserResource($this->whenLoaded('user')),
            'questions' => QuestionResource::collection($this->whenLoaded('questions')),

            // Computed fields
            'is_owner' => $this->user_id === auth()->id(),
            'can_edit' => $this->user_id === auth()->id() && $this->status !== 'closed',
        ];
    }
}

// Collection Resource for pagination
class SurveyCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'current_page' => $this->currentPage(),
                'total' => $this->total(),
                'per_page' => $this->perPage(),
                'last_page' => $this->lastPage(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
            ],
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
        ];
    }
}
```

### 2. API Versioning Strategy

```php
// routes/api.php
Route::prefix('v1')->group(function () {
    Route::apiResource('surveys', V1\SurveyController::class);
});

Route::prefix('v2')->group(function () {
    Route::apiResource('surveys', V2\SurveyController::class);
});

// Version-specific controller
class V2\SurveyController extends Controller
{
    public function index(Request $request)
    {
        $surveys = Survey::with(['user', 'questions'])
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->paginate($request->per_page ?? 15);

        return new SurveyCollection($surveys);
    }
}
```

### 3. Error Handling and Validation

```php
// Custom Exception Handler
class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
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
        }

        return parent::render($request, $exception);
    }
}

// Form Request with custom validation
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
            'settings.max_responses' => 'nullable|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'title.unique' => 'A survey with this title already exists.',
            'status.in' => 'Status must be one of: draft, published, closed.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->title),
        ]);
    }
}
```

## Laravel Ecosystem

### 1. Popular Packages

```php
// composer.json
{
    "require": {
        "laravel/sanctum": "^4.2",
        "spatie/laravel-query-builder": "^6.3",
        "spatie/laravel-permission": "^6.0",
        "spatie/laravel-activitylog": "^4.7",
        "laravel/horizon": "^5.21",
        "laravel/telescope": "^4.15"
    }
}

// Query Builder (like Prisma's filtering)
use Spatie\QueryBuilder\QueryBuilder;

$surveys = QueryBuilder::for(Survey::class)
    ->allowedFilters(['title', 'status', 'user.name'])
    ->allowedSorts(['created_at', 'title'])
    ->allowedIncludes(['user', 'questions'])
    ->paginate();

// Permission system
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Create roles and permissions
$adminRole = Role::create(['name' => 'admin']);
$userRole = Role::create(['name' => 'user']);

$createSurveyPermission = Permission::create(['name' => 'create surveys']);
$editSurveyPermission = Permission::create(['name' => 'edit surveys']);

$adminRole->givePermissionTo([$createSurveyPermission, $editSurveyPermission]);
$userRole->givePermissionTo($createSurveyPermission);
```

### 2. Queue System for Background Jobs

```php
// Job class
class ProcessSurveyResponse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Survey $survey,
        private array $responseData
    ) {}

    public function handle()
    {
        // Process response data
        $response = $this->survey->responses()->create($this->responseData);

        // Send notifications
        if ($this->survey->user->wants_notifications) {
            $this->survey->user->notify(new NewSurveyResponse($response));
        }

        // Update analytics
        $this->updateSurveyAnalytics($this->survey);
    }
}

// Dispatch job
ProcessSurveyResponse::dispatch($survey, $responseData);

// Queue configuration
// config/queue.php
'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => env('REDIS_QUEUE', 'default'),
        'retry_after' => 90,
        'block_for' => null,
    ],
],
```

### 3. Caching Strategies

```php
// Cache implementation
class SurveyService
{
    public function getPublishedSurveys()
    {
        return Cache::remember('published_surveys', 3600, function () {
            return Survey::with(['user', 'questions'])
                ->where('status', 'published')
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function getUserSurveys($userId)
    {
        $cacheKey = "user_surveys_{$userId}";

        return Cache::tags(['surveys', "user_{$userId}"])
            ->remember($cacheKey, 1800, function () use ($userId) {
                return Survey::where('user_id', $userId)
                    ->with('questions')
                    ->get();
            });
    }

    public function clearUserCache($userId)
    {
        Cache::tags(["user_{$userId}"])->flush();
    }
}

// Cache tags usage
Cache::tags(['surveys'])->flush(); // Clear all survey caches
```

## Performance & Optimization

### 1. Database Optimization

```php
// Eager loading to prevent N+1 queries
$surveys = Survey::with([
    'user:id,name,email',
    'questions:id,survey_id,title,type',
    'responses' => function ($query) {
        $query->select('id', 'survey_id', 'created_at');
    }
])->get();

// Query optimization
$surveys = Survey::select(['id', 'title', 'status', 'user_id', 'created_at'])
    ->with(['user:id,name'])
    ->where('status', 'published')
    ->orderBy('created_at', 'desc')
    ->paginate(15);

// Database indexing
// Migration
Schema::table('surveys', function (Blueprint $table) {
    $table->index(['status', 'created_at']);
    $table->index(['user_id', 'status']);
});
```

### 2. API Response Optimization

```php
// Conditional loading
class SurveyController extends Controller
{
    public function index(Request $request)
    {
        $query = Survey::query();

        // Only load relationships when needed
        if ($request->has('include')) {
            $includes = explode(',', $request->include);

            if (in_array('user', $includes)) {
                $query->with('user:id,name,email');
            }

            if (in_array('questions', $includes)) {
                $query->with('questions:id,survey_id,title,type');
            }
        }

        return SurveyResource::collection($query->paginate());
    }
}
```

### 3. Memory and Performance Monitoring

```php
// Performance monitoring
class SurveyController extends Controller
{
    public function index()
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $surveys = Survey::with('user')->paginate();

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        Log::info('Survey index performance', [
            'execution_time' => $endTime - $startTime,
            'memory_usage' => $endMemory - $startMemory,
            'surveys_count' => $surveys->count(),
        ]);

        return SurveyResource::collection($surveys);
    }
}
```

This deep dive covers the essential Laravel concepts that will help you transition from Next.js development. The patterns shown here are based on the actual project structure and represent production-ready practices for building robust APIs with Laravel.
