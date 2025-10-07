<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'survey_id',
        'title',
        'description',
        'type',
        'options',
        'validation_rules',
        'is_required',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
        'validation_rules' => 'array',
        'is_required' => 'boolean',
    ];

    /**
     * Get the survey that owns the question.
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Get the answers for the question.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Get question types with their display names.
     */
    public static function getQuestionTypes(): array
    {
        return [
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
    }

    /**
     * Check if question requires options (multiple choice, dropdown, etc.).
     */
    public function requiresOptions(): bool
    {
        return in_array($this->type, [
            'multiple_choice_single',
            'multiple_choice_multiple',
            'dropdown',
            'checkbox',
        ]);
    }

    /**
     * Check if question is a rating scale.
     */
    public function isRatingScale(): bool
    {
        return $this->type === 'rating_scale';
    }
}
