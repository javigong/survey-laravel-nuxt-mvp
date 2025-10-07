<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSurveyRequest;
use App\Http\Resources\SurveyResource;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class SurveyController extends Controller
{
    /**
     * Display a listing of the authenticated user's surveys.
     */
    public function index(): JsonResponse
    {
        $surveys = QueryBuilder::for(Survey::class)
            ->where('user_id', Auth::id())
            ->allowedFilters(['title', 'status'])
            ->allowedSorts(['created_at', 'title', 'updated_at'])
            ->defaultSort('-created_at')
            ->allowedIncludes(['questions'])
            ->paginate(15);

        return SurveyResource::collection($surveys)->response();
    }

    /**
     * Store a newly created survey.
     */
    public function store(StoreSurveyRequest $request): JsonResponse
    {
        $survey = Survey::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? 'draft',
        ]);

        return SurveyResource::make($survey)
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified survey.
     */
    public function show(Survey $survey): JsonResponse
    {
        // Ensure the survey belongs to the authenticated user
        $this->authorize('view', $survey);

        return SurveyResource::make($survey)->response();
    }

    /**
     * Update the specified survey.
     */
    public function update(StoreSurveyRequest $request, Survey $survey): JsonResponse
    {
        // Ensure the survey belongs to the authenticated user
        $this->authorize('update', $survey);

        $survey->update($request->validated());

        return SurveyResource::make($survey)->response();
    }

    /**
     * Remove the specified survey.
     */
    public function destroy(Survey $survey): JsonResponse
    {
        // Ensure the survey belongs to the authenticated user
        $this->authorize('delete', $survey);

        $survey->delete();

        return response()->json([
            'message' => 'Survey deleted successfully'
        ]);
    }
}
