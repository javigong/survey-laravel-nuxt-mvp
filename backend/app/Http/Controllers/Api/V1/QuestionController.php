<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    /**
     * Display a listing of questions for a specific survey.
     */
    public function index(Survey $survey): JsonResponse
    {
        // Ensure the survey belongs to the authenticated user
        $this->authorize('view', $survey);

        $questions = $survey->questions()->orderBy('order')->get();

        return QuestionResource::collection($questions)->response();
    }

    /**
     * Display a listing of questions for a published survey (public access).
     */
    public function indexPublic(Survey $survey): JsonResponse
    {
        // Only allow access to published surveys
        if ($survey->status !== 'published') {
            return response()->json([
                'message' => 'Survey not found or not available'
            ], 404);
        }

        $questions = $survey->questions()->orderBy('order')->get();

        return QuestionResource::collection($questions)->response();
    }

    /**
     * Store a newly created question.
     */
    public function store(StoreQuestionRequest $request): JsonResponse
    {
        $survey = Survey::findOrFail($request->survey_id);
        
        // Ensure the survey belongs to the authenticated user
        $this->authorize('update', $survey);

        // Set default order if not provided
        $order = $request->order ?? $survey->questions()->max('order') + 1;

        $question = Question::create([
            'survey_id' => $request->survey_id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'options' => $request->options,
            'validation_rules' => $request->validation_rules,
            'is_required' => $request->is_required ?? false,
            'order' => $order,
        ]);

        return QuestionResource::make($question)
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified question.
     */
    public function show(Question $question): JsonResponse
    {
        // Ensure the question's survey belongs to the authenticated user
        $this->authorize('view', $question->survey);

        return QuestionResource::make($question)->response();
    }

    /**
     * Update the specified question.
     */
    public function update(StoreQuestionRequest $request, Question $question): JsonResponse
    {
        // Ensure the question's survey belongs to the authenticated user
        $this->authorize('update', $question->survey);

        $question->update($request->validated());

        return QuestionResource::make($question)->response();
    }

    /**
     * Remove the specified question.
     */
    public function destroy(Question $question): JsonResponse
    {
        // Ensure the question's survey belongs to the authenticated user
        $this->authorize('update', $question->survey);

        $question->delete();

        return response()->json([
            'message' => 'Question deleted successfully'
        ]);
    }

    /**
     * Reorder questions within a survey.
     */
    public function reorder(Survey $survey): JsonResponse
    {
        // Ensure the survey belongs to the authenticated user
        $this->authorize('update', $survey);

        $request = request();
        $questionIds = $request->input('question_ids', []);

        if (empty($questionIds)) {
            return response()->json([
                'message' => 'No question IDs provided'
            ], 400);
        }

        // Update the order of questions
        foreach ($questionIds as $index => $questionId) {
            Question::where('id', $questionId)
                ->where('survey_id', $survey->id)
                ->update(['order' => $index + 1]);
        }

        return response()->json([
            'message' => 'Questions reordered successfully'
        ]);
    }
}
