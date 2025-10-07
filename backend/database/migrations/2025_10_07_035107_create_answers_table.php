<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade')->index();
            $table->foreignId('survey_id')->constrained()->onDelete('cascade')->index();
            $table->string('respondent_id')->nullable(); // Anonymous or user ID
            $table->text('text_answer')->nullable(); // For text-based answers
            $table->json('selected_options')->nullable(); // For multiple choice answers
            $table->integer('rating_value')->nullable(); // For rating scale answers
            $table->boolean('boolean_answer')->nullable(); // For yes/no answers
            $table->date('date_answer')->nullable(); // For date answers
            $table->time('time_answer')->nullable(); // For time answers
            $table->datetime('datetime_answer')->nullable(); // For datetime answers
            $table->string('file_path')->nullable(); // For file upload answers
            $table->string('file_name')->nullable(); // Original file name
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['survey_id', 'respondent_id']);
            $table->index(['question_id', 'respondent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
