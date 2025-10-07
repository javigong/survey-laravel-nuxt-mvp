<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    protected $fillable = [
        'question_id',
        'survey_id',
        'respondent_id',
        'text_answer',
        'selected_options',
        'rating_value',
        'boolean_answer',
        'date_answer',
        'time_answer',
        'datetime_answer',
        'file_path',
        'file_name',
    ];

    protected $casts = [
        'selected_options' => 'array',
        'boolean_answer' => 'boolean',
        'date_answer' => 'date',
        'time_answer' => 'datetime',
        'datetime_answer' => 'datetime',
    ];

    /**
     * Get the question that owns the answer.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the survey that owns the answer.
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Get the formatted answer value based on question type.
     */
    public function getFormattedAnswerAttribute(): mixed
    {
        return match ($this->question->type) {
            'multiple_choice_single', 'multiple_choice_multiple', 'dropdown', 'checkbox' => $this->selected_options,
            'text_short', 'text_long' => $this->text_answer,
            'rating_scale' => $this->rating_value,
            'yes_no' => $this->boolean_answer ? 'Yes' : 'No',
            'date' => $this->date_answer?->format('Y-m-d'),
            'time' => $this->time_answer?->format('H:i'),
            'datetime' => $this->datetime_answer?->format('Y-m-d H:i:s'),
            'file_upload' => $this->file_name,
            default => null,
        };
    }
}
