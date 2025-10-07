<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'survey_id' => $this->survey_id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'type_display' => $this->getQuestionTypeDisplayName(),
            'options' => $this->options,
            'validation_rules' => $this->validation_rules,
            'is_required' => $this->is_required,
            'order' => $this->order,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Get the display name for the question type.
     */
    private function getQuestionTypeDisplayName(): string
    {
        $types = [
            'multiple_choice_single' => 'Multiple Choice (Single)',
            'multiple_choice_multiple' => 'Multiple Choice (Multiple)',
            'text_short' => 'Short Text',
            'text_long' => 'Long Text',
            'rating_scale' => 'Rating Scale',
            'yes_no' => 'Yes/No',
            'dropdown' => 'Dropdown',
            'checkbox' => 'Checkbox',
            'date' => 'Date',
            'time' => 'Time',
            'datetime' => 'Date & Time',
            'file_upload' => 'File Upload',
        ];

        return $types[$this->type] ?? $this->type;
    }
}
