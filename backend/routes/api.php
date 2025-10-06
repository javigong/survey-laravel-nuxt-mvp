<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\SurveyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// API Version 1 Routes
Route::prefix('v1')->group(function () {

    // Public authentication routes (no auth required)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // CSRF cookie route for SPA authentication
    Route::get('/sanctum/csrf-cookie', function (Request $request) {
        return response()->json(['message' => 'CSRF cookie set']);
    })->middleware('web');

    // Protected routes (authentication required)
    Route::middleware('auth:sanctum')->group(function () {

        // Authentication routes
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        // Survey resource routes
        Route::apiResource('surveys', SurveyController::class);

        // Additional survey-related routes can be added here
        // Route::get('/surveys/{survey}/questions', [QuestionController::class, 'index']);
        // Route::get('/surveys/{survey}/responses', [ResponseController::class, 'index']);

    });

});

/*
|--------------------------------------------------------------------------
| API Route Structure Documentation
|--------------------------------------------------------------------------
|
| The API follows RESTful conventions with the following structure:
|
| Authentication Endpoints:
| POST   /api/v1/register          - User registration
| POST   /api/v1/login             - User login
| POST   /api/v1/logout            - User logout (protected)
| GET    /api/v1/user              - Get authenticated user (protected)
| GET    /api/v1/sanctum/csrf-cookie - CSRF cookie for SPA auth
|
| Survey Endpoints:
| GET    /api/v1/surveys           - List all surveys (protected)
| POST   /api/v1/surveys           - Create new survey (protected)
| GET    /api/v1/surveys/{id}      - Get specific survey (protected)
| PUT    /api/v1/surveys/{id}      - Update survey (protected)
| DELETE /api/v1/surveys/{id}      - Delete survey (protected)
|
| Rate Limiting:
| - API routes are protected by throttle middleware (60 requests per minute)
| - Authentication routes may have stricter limits
|
| Response Format:
| All API responses follow JSON:API specification with consistent structure
|
*/
