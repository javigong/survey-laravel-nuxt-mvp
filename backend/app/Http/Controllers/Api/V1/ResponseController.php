<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResponseRequest;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResponseController extends Controller
{
    /**
     * Store a new survey response.
     */
    public function store(Request $request, int $surveyId): JsonResponse
    {
        try {
            // Validate survey exists and is published
            $survey = Survey::findOrFail($surveyId);
            
            if ($survey->status !== 'published') {
                return response()->json([
                    'message' => 'Survey is not available for responses'
                ], 403);
            }

            // Validate request data
            $request->validate([
                'respondent_id' => 'required|string|max:255',
                'answers' => 'required|array',
                'answers.*.question_id' => 'required|integer|exists:questions,id',
                'answers.*.value' => 'required',
            ]);

            $respondentId = $request->input('respondent_id');
            $answers = $request->input('answers');

            // Verify all questions belong to this survey
            $questionIds = collect($answers)->pluck('question_id');
            $surveyQuestionIds = Question::where('survey_id', $surveyId)->pluck('id');
            
            if (!$questionIds->every(fn($id) => $surveyQuestionIds->contains($id))) {
                return response()->json([
                    'message' => 'Invalid question IDs provided'
                ], 422);
            }

            // Store answers
            foreach ($answers as $answerData) {
                $question = Question::find($answerData['question_id']);
                $value = $answerData['value'];

                // Prepare answer data based on question type
                $answerRecord = [
                    'question_id' => $question->id,
                    'survey_id' => $surveyId,
                    'respondent_id' => $respondentId,
                ];

                // Store answer based on question type
                switch ($question->type) {
                    case 'text_short':
                    case 'text_long':
                        $answerRecord['text_answer'] = $value;
                        break;
                    
                    case 'multiple_choice_single':
                        $answerRecord['selected_options'] = [$value];
                        break;
                    
                    case 'multiple_choice_multiple':
                        $answerRecord['selected_options'] = is_array($value) ? $value : [$value];
                        break;
                    
                    case 'rating_scale':
                        $answerRecord['rating_value'] = (int) $value;
                        break;
                    
                    case 'yes_no':
                        $answerRecord['boolean_answer'] = $value === 'yes';
                        break;
                    
                    case 'dropdown':
                        $answerRecord['selected_options'] = [$value];
                        break;
                    
                    case 'date':
                        $answerRecord['date_answer'] = $value;
                        break;
                    
                    case 'time':
                        $answerRecord['time_answer'] = $value;
                        break;
                    
                    case 'datetime':
                        $answerRecord['datetime_answer'] = $value;
                        break;
                    
                    case 'file_upload':
                        $answerRecord['file_name'] = $value;
                        $answerRecord['file_path'] = 'uploads/' . $value; // In real app, store actual file
                        break;
                }

                Answer::create($answerRecord);
            }

            return response()->json([
                'message' => 'Response submitted successfully',
                'respondent_id' => $respondentId,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to submit response',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get survey responses (for survey owner).
     */
    public function index(Request $request, int $surveyId): JsonResponse
    {
        try {
            $survey = Survey::findOrFail($surveyId);
            
            // Check if user owns this survey
            if ($survey->user_id !== Auth::id()) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 403);
            }

            $responses = Answer::where('survey_id', $surveyId)
                ->with('question')
                ->get()
                ->groupBy('respondent_id');

            return response()->json([
                'data' => $responses,
                'message' => 'Responses retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve responses',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
