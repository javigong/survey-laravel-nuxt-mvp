<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $questionTypes = [
            'multiple_choice_single',
            'multiple_choice_multiple',
            'text_short',
            'text_long',
            'rating_scale',
            'yes_no',
            'dropdown',
            'checkbox',
            'date',
            'time',
            'datetime',
            'file_upload'
        ];

        return [
            'survey_id' => 'required|exists:surveys,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|string|in:' . implode(',', $questionTypes),
            'options' => 'nullable|array',
            'options.*' => 'string|max:255',
            'validation_rules' => 'nullable|array',
            'is_required' => 'boolean',
            'order' => 'integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'survey_id.required' => 'Survey ID is required.',
            'survey_id.exists' => 'The selected survey does not exist.',
            'title.required' => 'Question title is required.',
            'title.max' => 'Question title may not be greater than 255 characters.',
            'type.required' => 'Question type is required.',
            'type.in' => 'The selected question type is invalid.',
            'options.array' => 'Options must be an array.',
            'options.*.string' => 'Each option must be a string.',
            'options.*.max' => 'Each option may not be greater than 255 characters.',
            'validation_rules.array' => 'Validation rules must be an array.',
            'is_required.boolean' => 'Required field must be true or false.',
            'order.integer' => 'Order must be an integer.',
            'order.min' => 'Order must be at least 0.',
        ];
    }
}
